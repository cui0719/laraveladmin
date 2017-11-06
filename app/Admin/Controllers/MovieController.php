<?php

namespace App\Admin\Controllers;

use App\Movie;
use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Admin\Extensions\Tools\UserGender;
use Illuminate\Support\Facades\Request;

class MovieController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('电影');
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('新增');
            $content->description('电影');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Movie::class, function (Grid $grid) {
            if (in_array(Request::get('gender'), ['m', 'f'])) {
                $grid->model()->where('gender', Request::get('gender'));
            }

            // 第一列显示id字段，并将这一列设置为可排序列
            $grid->id('ID')->sortable();
            // 第二列显示title字段，由于title字段名和Grid对象的title方法冲突，所以用Grid的column()方法代替
            $grid->column('title','电影');

            $grid->actor('女主角')->display(function($userId) {
                return User::find($userId)->name;
            });
            // 第三列显示director字段，通过display($callback)方法设置这一列的显示内容为users表中对应的用户名
            $grid->director('男主角')->display(function($userId) {
                return User::find($userId)->name;
            });
            // 第四列显示为describe字段
            $grid->describe('简介')->popover('right');
            // 第五列显示为rate字段
            $grid->rate('上座率');
            // 第六列显示released字段，通过display($callback)方法来格式化显示输出
//            $grid->released('上映?')->display(function ($released) {
//                return $released ? '是' : '否';
//            });
            $states = [
                'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '否', 'color' => 'default'],
            ];
            $grid->released('上映?')->switch($states);

            $grid->tools(function ($tools) {
                $tools->append(new UserGender());
            });

            // 下面为三个时间字段的列显示
            $grid->release_at('上映时间');
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');

            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function ($filter) {
                // 设置created_at字段的范围查询
                $filter->between('created_at', '创建时间')->datetime();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Movie::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('title', '电影')->rules('required');
            $form->select('actor', '女主角')->options(User::all()->pluck('name', 'id'));
            $form->select('director', '男主角')->options(User::all()->pluck('name', 'id'));
            $form->text('describe','簡介');

            $states = [
                'on'  => ['value' => 1, 'text' => '上映', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '未上映', 'color' => 'default'],
            ];
            $form->switch('released','上映情况')->states($states);
            $form->number('rate', '打分');



            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}

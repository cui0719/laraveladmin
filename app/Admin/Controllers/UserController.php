<?php

namespace App\Admin\Controllers;

use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Admin\Extensions\CheckRow;

class UserController extends Controller
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

            $content->header('用户');
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

            $content->header('修改用户');
            $content->description('');

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

            $content->header('header');
            $content->description('description');

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
            // $grid->id('ID')->sortable();
            // $grid->username(trans('admin.username'));
            // $grid->name(trans('admin.name'));
            // $grid->roles(trans('admin.roles'))->pluck('name')->label();
            // $grid->created_at(trans('admin.created_at'));
            // $grid->updated_at(trans('admin.updated_at'));

            // $grid->actions(function (Grid\Displayers\Actions $actions) {
            //     if ($actions->getKey() == 1) {
            //         $actions->disableDelete();
            //     }
            // });

            // $grid->tools(function (Grid\Tools $tools) {
            //     $tools->batch(function (Grid\Tools\BatchActions $actions) {
            //         $actions->disableDelete();
            //     });
            // });

        return Admin::grid(User::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->model()->orderBy('id', 'desc');
            $grid->name('用户名')->color('#ccc');
            $grid->email('邮箱')->badge('danger');
//            $grid->photo('头像');
            $grid->photo('头像')->image(config('app.url').'/uploads/', 100, 100);

            $grid->profile()->age('年龄');
            $grid->profile()->gender('性别');

            $grid->created_at()->editable('datetime');
            $grid->updated_at()->editable('datetime');

//            $grid->actions(function ($actions) {
//
//                // append一个操作
//                $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
//
//                // prepend一个操作
//                $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
//            });
            $grid->actions(function ($actions) {
                // 添加操作
                $actions->append(new CheckRow($actions->getKey()));
                // append一个操作
                $actions->append('<a href="users/edit"><i class="fa fa-eye" title="预览"></i></a>');
                // prepend一个操作
                $actions->prepend('<a href=""><i class="fa fa-paper-plane" title="飞机"></i></a>');
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
        return Admin::form(User::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', '用户名');
            $form->text('email', '邮箱');
            $form->image('photo', '头像');
            $form->password('password', trans('密码'))->rules('required|confirmed')->default(function ($form) {
                return $form->model()->password;
            });
            $form->password('password_confirmation', trans('确认密码'))->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });
            $form->ignore(['password_confirmation']);

//            $form->profile()->text('age', '年龄');
//            $form->profile()->text('gender', '性别');
//            $form->multipleSelect('actor', '女主角')->options(User::all()->pluck('name', 'id'));
            $form->number('profile.age','年龄');
            $states = [
                'on'  => ['value' => 'm', 'text' => '男', 'color' => 'success'],
                'off' => ['value' => 'f', 'text' => '女', 'color' => 'danger'],
            ];
            $form->switch('profile.gender', '性别')->states($states);

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });
        });
    }
}

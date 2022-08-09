<?php

namespace Webkul\Admin;

class Bouncer
{
    protected $choose_guard = 'user';


    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public function hasPermission($permission)
    {

        if (auth()->guard($this->choose_guard)->check() && auth()->guard($this->choose_guard)->user()->role->permission_type == 'all') {
            return true;
        } else {
            if (! auth()->guard($this->choose_guard)->check() || ! auth()->guard($this->choose_guard)->user()->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public static function allow($permission)
    {
        $self = new static;
        if (! auth()->guard($self->choose_guard)->check() || ! auth()->guard($self->choose_guard)->user()->hasPermission($permission)) {
            abort(401, 'This action is unauthorized');
        }
    }
}
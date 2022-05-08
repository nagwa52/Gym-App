<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Session;

class NotOverlapped implements Rule {

    private $session_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($session_id) {
        $this->session_id = $session_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if ($this->session_id) {
            $check = Session::query()
                ->where('id', '!=', $this->session_id)
                ->whereBetween($attribute, [request()->starts_at, request()->finishes_at])->exists();
        } else {
            $check = Session::query()
                ->whereBetween($attribute, [request()->starts_at, request()->finishes_at])->exists();
        }

        return !$check;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return 'The selected period is overlapping with either starting time or ending time of other session.';
    }
}

<?php

namespace App\Traits;

trait Timestamp
{
    /**
     * Returns the Unix timestamp of the latest update
     *
     * @return number
     */
    public function timestamp() {
        return strtotime($this->updated_at);
    }
}

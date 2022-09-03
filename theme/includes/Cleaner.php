<?php


class Cleaner
{
    public function __construct()
    {
        $this->removeBasicThumbnails();
    }

    public function removeBasicThumbnails()
    {
        update_option('thumbnail_size_h', 0);
        update_option('thumbnail_size_w', 0);
        update_option('medium_size_h', 0);
        update_option('medium_size_w', 0);
        update_option('large_size_h', 0);
        update_option('large_size_w', 0);
    }
}

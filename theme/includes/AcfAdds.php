<?php

class AcfAdds
{
    public static function getOptions()
    {
        return get_fields('options');
    }

}
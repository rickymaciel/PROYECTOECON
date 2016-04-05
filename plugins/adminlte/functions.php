<?php

/*
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2016, Carlos García Gómez. All Rights Reserved. 
 */

if( !function_exists('get_gravatar') )
{
   function get_gravatar($email, $size=80)
   {
      return "https://www.gravatar.com/avatar/".md5( strtolower( trim($email) ) )."?s=".$size;
   }
}
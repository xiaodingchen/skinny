<?php 
	class lib_facades_view extends lib_facades_facade{
		private static $__view;

		protected static function getFacadeAccessor()
		{
			if(! static::$__view)
			{
				static::$__view = kernel::single('lib_static_view');
			}

			return static::$__view;
		}
	}

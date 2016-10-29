<?php

class lib_validator_factory {

	/**
	 * Create a new Validator instance.
	 *
	 * @param  array  $data
	 * @param  array  $rules
	 * @param  array  $messages
	 * @return \lib_validator_validator
	 */
	public function make(array $data, array $rules, array $messages = array())
	{

		$validator = new lib_validator_validator($data,$rules,$messages);

		return $validator;

	}

}

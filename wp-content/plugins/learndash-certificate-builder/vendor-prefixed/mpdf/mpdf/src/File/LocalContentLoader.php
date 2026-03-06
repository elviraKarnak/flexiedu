<?php

namespace LearnDash\Certificate_Builder\Mpdf\File;

class LocalContentLoader implements \LearnDash\Certificate_Builder\Mpdf\File\LocalContentLoaderInterface
{

	public function load($path)
	{
		return file_get_contents($path);
	}

}

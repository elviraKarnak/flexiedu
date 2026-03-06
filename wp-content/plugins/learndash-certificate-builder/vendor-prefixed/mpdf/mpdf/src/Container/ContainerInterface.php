<?php

namespace LearnDash\Certificate_Builder\Mpdf\Container;

interface ContainerInterface
{

	public function get($id);

	public function has($id);

}

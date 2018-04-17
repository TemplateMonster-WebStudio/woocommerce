<?php 

namespace extensions\woocommerce\inc;

abstract class AbstractWooIntegrator{
	abstract public function output_content_wrapper_start();
	abstract public function output_content_wrapper_end();
	abstract public function after_sidebar_wrapper_close();
}

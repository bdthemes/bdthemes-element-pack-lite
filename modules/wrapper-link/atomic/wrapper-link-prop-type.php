<?php

namespace ElementPack\Modules\WrapperLink\Atomic;

use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Link_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Query_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Url_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wrapper_Link_Prop_Type extends Link_Prop_Type {
	private const PROP_NAME = 'element_pack_wrapper_link_url';

	protected function define_shape(): array {
		$target_blank_dependencies = Dependency_Manager::make()
			->where(
				[
					'operator' => 'exists',
					'path' => [ self::PROP_NAME, 'destination' ],
				]
			)
			->get();

		return [
			'destination' => Union_Prop_Type::make()
				->add_prop_type( Url_Prop_Type::make()->skip_validation() )
				->add_prop_type( Query_Prop_Type::make() ),
			'isTargetBlank' => Boolean_Prop_Type::make()
				->set_dependencies( $target_blank_dependencies ),
			'tag' => String_Prop_Type::make()
				->enum( [ 'a', 'button' ] )
				->default( self::DEFAULT_TAG ),
		];
	}
}

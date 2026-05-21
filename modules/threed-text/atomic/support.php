<?php

namespace ElementPack\Modules\ThreedText\Atomic;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type;

if (! defined('ABSPATH')) {
	exit;
}

class Support
{
	public const ACTIVE_KEY = 'ep_threed_text_active';
	public const DEPTH_KEY = 'ep_threed_text_depth';
	public const LAYERS_KEY = 'ep_threed_text_layers';
	public const DEPTH_COLOR_KEY = 'ep_threed_text_depth_color';
	public const PERSPECTIVE_KEY = 'ep_threed_text_perspective';
	public const FADE_KEY = 'ep_threed_text_fade';
	public const EVENT_KEY = 'ep_threed_text_event';
	public const EVENT_ROTATION_KEY = 'ep_threed_text_event_rotation';
	public const EVENT_DIRECTION_KEY = 'ep_threed_text_event_direction';

	public function register_schema(array $schema): array
	{
		$active_dependencies = $this->get_active_dependencies();
		$active_event_dependencies = $this->get_active_event_dependencies();

		$schema[self::ACTIVE_KEY] = $schema[self::ACTIVE_KEY] ?? Boolean_Prop_Type::make()->default(false);
		$schema[self::DEPTH_KEY] = $schema[self::DEPTH_KEY] ?? $this->number_schema(30, $active_dependencies);
		$schema[self::LAYERS_KEY] = $schema[self::LAYERS_KEY] ?? $this->number_schema(8, $active_dependencies, false);
		$schema[self::DEPTH_COLOR_KEY] = $schema[self::DEPTH_COLOR_KEY] ?? String_Prop_Type::make()
			->default('')
			->set_dependencies($active_dependencies);
		$schema[self::PERSPECTIVE_KEY] = $schema[self::PERSPECTIVE_KEY] ?? $this->number_schema(500, $active_dependencies);
		$schema[self::FADE_KEY] = $schema[self::FADE_KEY] ?? Boolean_Prop_Type::make()
			->default(true)
			->set_dependencies($active_dependencies);
		$schema[self::EVENT_KEY] = $schema[self::EVENT_KEY] ?? String_Prop_Type::make()
			->default('none')
			->set_dependencies($active_dependencies);
		$schema[self::EVENT_ROTATION_KEY] = $schema[self::EVENT_ROTATION_KEY] ?? $this->number_schema(35, $active_event_dependencies);
		$schema[self::EVENT_DIRECTION_KEY] = $schema[self::EVENT_DIRECTION_KEY] ?? String_Prop_Type::make()
			->default('default')
			->set_dependencies($active_event_dependencies);

		return $schema;
	}

	private function number_schema($default, ?array $dependencies, bool $float = true): Union_Prop_Type
	{
		$number_schema = Number_Prop_Type::make();

		if ($float) {
			$number_schema->float();
		}

		return Union_Prop_Type::make()
			->add_prop_type($number_schema)
			->add_prop_type(String_Prop_Type::make())
			->default($default, 'number')
			->set_dependencies($dependencies);
	}

	public function register_controls(array $controls): array
	{
		foreach ($controls as $item) {
			if (! ($item instanceof Section) || 'settings' !== $item->get_id()) {
				continue;
			}

			$item->add_item(
				Switch_Control::bind_to(self::ACTIVE_KEY)
					->set_label(esc_html__('3D Text', 'bdthemes-element-pack'))
					->set_meta(['topDivider' => true])
			);

			$item->add_item(
				Number_Control::bind_to(self::DEPTH_KEY)
					->set_label(esc_html__('Depth', 'bdthemes-element-pack'))
					->set_min(0)
					->set_max(100)
					->set_step(1)
			);

			$item->add_item(
				Number_Control::bind_to(self::LAYERS_KEY)
					->set_label(esc_html__('Layers', 'bdthemes-element-pack'))
					->set_min(0)
					->set_max(100)
					->set_step(1)
			);

			$item->add_item(
				Text_Control::bind_to(self::DEPTH_COLOR_KEY)
					->set_label(esc_html__('Depth Color', 'bdthemes-element-pack'))
			);

			$item->add_item(
				Number_Control::bind_to(self::PERSPECTIVE_KEY)
					->set_label(esc_html__('Perspective', 'bdthemes-element-pack'))
					->set_min(0)
					->set_max(1000)
					->set_step(1)
			);

			$item->add_item(
				Switch_Control::bind_to(self::FADE_KEY)
					->set_label(esc_html__('Fade', 'bdthemes-element-pack'))
			);

			$item->add_item(
				Select_Control::bind_to(self::EVENT_KEY)
					->set_label(esc_html__('Event', 'bdthemes-element-pack'))
					->set_options(
						[
							['value' => 'none', 'label' => esc_html__('None', 'bdthemes-element-pack')],
							['value' => 'pointer', 'label' => esc_html__('Pointer', 'bdthemes-element-pack')],
							['value' => 'scroll', 'label' => esc_html__('Scroll', 'bdthemes-element-pack')],
							['value' => 'scrollX', 'label' => esc_html__('ScrollX', 'bdthemes-element-pack')],
							['value' => 'scrollY', 'label' => esc_html__('ScrollY', 'bdthemes-element-pack')],
						]
					)
			);

			$item->add_item(
				Number_Control::bind_to(self::EVENT_ROTATION_KEY)
					->set_label(esc_html__('Event Rotation', 'bdthemes-element-pack'))
					->set_min(-360)
					->set_max(360)
					->set_step(1)
			);

			$item->add_item(
				Select_Control::bind_to(self::EVENT_DIRECTION_KEY)
					->set_label(esc_html__('Event Direction', 'bdthemes-element-pack'))
					->set_options(
						[
							['value' => 'default', 'label' => esc_html__('Default', 'bdthemes-element-pack')],
							['value' => 'reverse', 'label' => esc_html__('Reverse', 'bdthemes-element-pack')],
						]
					)
			);

			break;
		}

		return $controls;
	}

	private function get_active_dependencies(): ?array
	{
		return Dependency_Manager::make(Dependency_Manager::RELATION_AND)
			->where(
				[
					'operator' => 'eq',
					'path' => [self::ACTIVE_KEY],
					'value' => true,
					'effect' => 'hide',
				]
			)
			->get();
	}

	private function get_active_event_dependencies(): ?array
	{
		return Dependency_Manager::make(Dependency_Manager::RELATION_AND)
			->where(
				[
					'operator' => 'eq',
					'path' => [self::ACTIVE_KEY],
					'value' => true,
					'effect' => 'hide',
				],
			)
			->where(
				[
					'operator' => 'ne',
					'path' => [self::EVENT_KEY],
					'value' => 'none',
					'effect' => 'hide',
				]
			)
			->get();
	}
}

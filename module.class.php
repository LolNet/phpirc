<?php
/**
 * Module
 *
 * @author Gussi <gussi@gussi.is>
 */

abstract class module {
	protected $parent;				/** Parent phpirc object */
	private $event;					/** Event manager */
	private $timer;					/** Timer manager */
	public $config;

	/**
	 * module constructor
	 *
	 * @param phpirc $parent				Parent instance
	 */
	public function __construct(phpirc $parent) {
		if(!is_null($parent->module(get_class($this)))) {
			printf("ERR: Module '%s' already loaded\n", get_class($this));
			return FALSE;
		}

		$this->parent = $parent;
		$this->event = new event($this);
		$this->timer = new timer($this);

		$module_name = array_pop(explode('_', get_class($this), 2));
		if(isset($this->parent->config['module'][$module_name])) {
			$this->config = $this->parent->config['module'][$module_name];
		} else {
			$this->config = array();
		}

		printf("INFO: Module '%s' loaded\n", get_class($this));
	}

	/**
	 * Module initialization
	 */
	public function init() {
		// NOOP
	}

	/**
	 * Module cleanup
	 */
	public function clean(){
		// NOOP
	}

	/**
	 * Get parent
	 */
	final public function parent() {
		return $this->parent;
	}

	/**
	 * Process recieved data, for raw usage
	 *
	 * @param string $data						Data recieved
	 */
	final public function process($data = NULL) {
		if(!is_null($data)) {
			if($data[0] == ':') {
				list($prefix, $cmd, $j) = explode(' ', $data, 3);
			} else {
				list($cmd, $rest) = explode(' ', $data, 2);
			}
			$this->event->notify($cmd, $data);
		}
		$this->timer->tick();
	}

	/**
	 * Return loaded module
	 *
	 * @return module						Return module, or NULL if it isn't loaded
	 */
	final private function module($module_name) {
		return $this->parent->module($module_name);
	}

	/**
	 * Subscribe to event, automatically register module as owner
	 *
	 * @see event:subscribe
	 */
	final protected function event($event, $callback) {
		$this->event->subscribe($event, $callback);
	}

	/**
	 * Set timer
	 *
	 * @see timer::start
	 */
	final public function timer($seconds, $callback, $type = timer::TIMER_INTERVAL) {
		$this->timer->start($seconds, $callback, $type);
	}
}

<?php

namespace OpenCloud\LoadBalancer;

/**
 * sub-resource to manage algorithms (read-only)
 */
class Algorithm extends \OpenCloud\AbstractClass\PersistentObject {
	public
		$name;
	protected static
		$json_name = 'algorithm',
		$url_resource = 'loadbalancers/algorithms';
	public function Create($params=array()) { $this->NoCreate(); }
	public function Update($params=array()) { $this->NoUpdate(); }
	public function Delete() { $this->NoDelete(); }
}
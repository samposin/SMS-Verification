<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Preview;

use Twilio\Domain;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Preview\DeployedDevices\FleetList;
use Twilio\Version;

/**
 * @property \Twilio\Rest\Preview\DeployedDevices\FleetList fleets
 * @method \Twilio\Rest\Preview\DeployedDevices\FleetContext fleets(string $sid)
 */
class DeployedDevices extends Version {
    protected $_fleets = null;

    /**
     * Construct the DeployedDevices version of Preview
     * 
     * @param \Twilio\Domain $domain Domain that contains the version
     * @return \Twilio\Rest\Preview\DeployedDevices DeployedDevices version of
     *                                              Preview
     */
    public function __construct(Domain $domain) {
        parent::__construct($domain);
        $this->version = 'DeployedDevices';
    }

    /**
     * @return \Twilio\Rest\Preview\DeployedDevices\FleetList 
     */
    protected function getFleets() {
        if (!$this->_fleets) {
            $this->_fleets = new FleetList($this);
        }
        return $this->_fleets;
    }

    /**
     * Magic getter to lazy load root resources
     * 
     * @param string $name Resource to return
     * @return \Twilio\ListResource The requested resource
     * @throws \Twilio\Exceptions\TwilioException For unknown resource
     */
    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new TwilioException('Unknown resource ' . $name);
    }

    /**
     * Magic caller to get resource contexts
     * 
     * @param string $name Resource to return
     * @param array $arguments Context parameters
     * @return \Twilio\InstanceContext The requested resource context
     * @throws \Twilio\Exceptions\TwilioException For unknown resource
     */
    public function __call($name, $arguments) {
        $property = $this->$name;
        if (method_exists($property, 'getContext')) {
            return call_user_func_array(array($property, 'getContext'), $arguments);
        }

        throw new TwilioException('Resource does not have a context');
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        return '[Twilio.Preview.DeployedDevices]';
    }
}
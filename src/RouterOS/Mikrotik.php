<?php

namespace RouterOS;

class Mikrotik {

    private $mikrotik = null;
    private $connected = true;

    /**
     * @param string $ip The IP Address of the Mikrotik
     * @param string $username The Username to access the Mikrotik
     * @param string $password The Password to access the Mikrotik
     */
    public function __construct($ip, $username, $password, $port = 8728) {

        $this->mikrotik = new Core();
        $this->mikrotik->port = $port;

        try {
            $this->connected = $this->mikrotik->connect($ip, $username, $password);
        } catch (Exception $e) {
            throw new Exception("unableToConnect");
        }
        

    }

    /**
     * Exempts a device by mac address.
     *
     * Adds a user account with the specified MAC address, then 
     * removes that user from the host table to make the device 
     * automatically re-authenticate
     *
     * @param string $mac The MAC Address of a device
     */
    public function exemptByMac($mac)
    {

        try {

            if ($this->connected == true) {

                $response = $this->mikrotik->comm(
                    '/ip/hotspot/host/print',
                    array (
                        "?mac-address" => $mac
                    )
                );

                if (is_array($response)) {
                    $response = $response[0];
                }

                $mac = $response['mac-address'];

                //ask the Mikrotik to set a user to active
                $response =  $this->mikrotik->comm(
                    '/ip/hotspot/user/add',
                    array(
                        //'customer' => 'admin',
                        'name' => $mac,
                        //'actual-profile' => PLAN,
                        'password' => 'indigo',
                    )
                );

                //echo "<pre>User Add". print_r($response, true) ."</pre>";

                $hostList = $this->mikrotik->comm(
                    '/ip/hotspot/host/print',
                    array(
                        "?mac-address" => $mac
                    )
                );

                //echo "<pre>HostList". print_r($hostList, true) ."</pre>";

                //ask the Mikrotik to remove IP-binding
                $bindResponse = $this->mikrotik->comm(
                    '/ip/hotspot/host/remove',
                    array (
                        ".id" => $hostList[0]['.id']
                    )
                );

                //echo "<pre>Bind Response" . print_r($bindResponse) ."</pre>";

                return $bindResponse;

            } else {
                throw new Exception("not connected");
            }

        } catch (Exception $e) {
            throw new Exeption("unknownError");
        }
        
    }

    /**
     * Retrieves the hosts table from a Mikrotik
     */
    public function get_hosts()
    {
        try {
            if ($this->connected == true) {
                
                $response = @$this->mikrotik->comm('/ip/hotspot/host/print');

                
                return $this->getHostObjects($response);
            } else {
                return false;
            }

        } catch (Exception $e) {
            throw new Exeption("unknownError");
        }
        
    }

    /**
     * Users get_hosts() to cound the number of total and active users in the host table
     */
    public function getHostsCount() {

        try {

            $hosts = $this->get_hosts();

            if(is_array($hosts)) {

                $total = count($hosts);

                $activeCount = 0;

                foreach($hosts as $host) {
                    if ($host->authorized == "true") {
                        $activeCount++;
                    }
                }

                return array("total" => $total, "active" => $activeCount);

            } else {
                return false;
            }

            

            
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Retrieves the Host Name of the device with the provided MAC address
     *
     * @param string $mac The MAC Address of a device
     */
    public function getHostName($mac) {

        try {

            $response = @$this->mikrotik->comm(
                "/ip/dhcp-server/lease/print",
                array (
                    "?mac-address" => $mac
                )
            );

            if (isset($response[0]['host-name'])) {
                return $response[0]['host-name'];
            } else {
                return "Not Provided";
            }
            


        } catch (Exception $e) {
            throw new Exemption("unknownError");
        }

    }

    /**
     * Removes "-" from array keys
     * @param mixed[][] $arr
     */
    public function getHostObjects($arr) 
    {

        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i] = $this->getHostObject($arr[$i]);
        }
        
        return $arr;
    }

    /**
     * Converts an array to a generic object
     *
     * Converts an array provided by the Mikrotik API for 
     * /ip/dhcp-server/lease/print and makes a generic PHP object
     * to rename variables
     *
     * @param mixed[] $arr The array provided by /ip/dhcp-server/lease/print
     */
    public function getHostObject($arr) {
        $temp = new \stdClass();
        $temp->macAddress = $arr['mac-address'];
        $temp->address = $arr['address'];
        $temp->toAddress = $arr['to-address'];
        $temp->server = $arr['server'];

        //$temp->hostName = 'Nick Test'; //$this->getHostName($temp->macAddress);

        $temp->uptime = $arr['uptime'];

        if (isset($arr['static'])) {
            $temp->static = $arr['static'];
        } else {
            $temp->static = "false";
        }
        

        $temp->authorized = $arr['authorized'];
        $temp->bypassed = $arr['bypassed'];
        return $temp;
    }

}
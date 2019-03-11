<?php

namespace ApexLegends\Api;

use ApexLegends\Client;

class User extends AbstractApi 
{
	const USER_ID_SEARCH_URL = 'https://api1.origin.com/atom/users';
    const USER_AVATAR_URL = 'https://api1.origin.com/avatar/user/%s/avatars?size=2';

    private $account;
	private $userId;
	private $avatar = '';

	public function __construct(Client $client, int $userId) 
	{
		parent::__construct($client);

		$this->userId = $userId;
	}

	/**
	 * Get EA account data.
	 *
	 * @return object Account data.
	 */
	public function data() : object
	{
		if ($this->account === null)
		{
			$this->account = $this->get(self::USER_ID_SEARCH_URL, [
				'userIds' => $this->id()
			]);
		}
		
		return $this->account;
	}
	
	/**
	 * Gets Apex stats.
	 * 
	 * NOTE: Right now, PC stats only seem to be given. I'm not sure if this is an endpoint issue or if something is different with the user ids between platforms.
	 * I will keep looking into this.
	 *
	 * @param string $console PC/PS4/Xbox.
	 * @return string Stats data as a string because this API is awful (will fix later).
	 */
	public function stats(string $console) : string
	{
		$response = $this->get(sprintf('https://r5-%s.stryder.respawn.com/user.php', strtolower($console)), [
			'qt' => 'user-getinfo',
			'getinfo' => 1,
			'hardware' => strtoupper($console),
			'uid' => $this->id(),
			'language' => 'english',
			'timezoneOffset' => 1,
			'ugc' => 1,
			'rep' => 1,
			'searching' => 0,
			'change' => 7,
			'loadidx' => 1
		]);

		return $response;
	}

	/**
	 * Get EA account's user ID.
	 *
	 * @return int User ID (-1 if error)
	 */
	public function id() : int
	{
		return $this->userId;
	}
    /**
     * Get EA account's user avatar.
     *
     * @return string User avatar (empty if error)
     */
    public function avatar() : string
    {
        if ($this->avatar === '')
        {
            $data = $this->get(sprintf(self::USER_AVATAR_URL, $this->id()));
            if (isset($data->user->avatar->link)) {
                $this->avatar = $data->user->avatar->link;
            }
        }

        return $this->avatar;
    }
}
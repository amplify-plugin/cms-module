<?php

namespace Amplify\System\Cms\Widgets\Menu;

use Amplify\Widget\Abstracts\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;

/**
 * @class ExchangeRewardMenu
 */
class ExchangeRewardMenu extends BaseComponent
{
    /**
     * @var array
     */
    public $options;

    public $clientToken;

    public $secret;

    public $baseUrl;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->clientToken = config('amplify.frontend.exchange_reward_client_token', '078bcc74');
        $this->secret = config('amplify.frontend.exchange_reward_secret', '5016e7d76f9c3111bf0d01773f4791ea');
        $this->baseUrl = config('amplify.frontend.exchange_baseurl', 'https://safetyproducts.online-rewards.com/rpc/');
    }

    /**
     * Whether the component should be rendered
     */
    public function shouldRender(): bool
    {
        return config('amplify.frontend.enable_exchange_reward', false);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('widget::menu.exchange-reward-menu');
    }

    public function htmlAttributes(): string
    {

        $this->attributes = $this->attributes->merge([
            'id' => 'show-exchage-account',
            'class' => 'account flex-shrink-0 w-logo',
        ]);

        return parent::htmlAttributes();
    }

    public function getMemberStatus()
    {

        $customer = customer(true);
        $rpcMethod = 'getmemberstatus';
        $userEmail = $customer->email;
        $message = 'rpc_method='.$rpcMethod.'&user_id='.$userEmail;
        $hmac = hash_hmac('md5', $message, $this->secret);

        try {
            $response = Http::get($this->baseUrl.$this->clientToken.'/'.$hmac.'/?'.'rpc_method='.$rpcMethod.'&user_id='.$userEmail);

            if ($response->status() === 200 && ! isset($response['error'])) {
                $memberStatus = $response['response'][0]['getmemberstatus'][0]['member_status'];

                return (bool) $memberStatus;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

    }

    public function getMemberBalance()
    {

        $customer = customer(true);
        $rpcMethod = 'getmemberbalance';
        $userEmail = $customer->email;
        $message = 'rpc_method='.$rpcMethod.'&user_id='.$userEmail;
        $hmac = hash_hmac('md5', $message, $this->secret);

        try {
            $response = Http::get($this->baseUrl.$this->clientToken.'/'.$hmac.'/?'.'rpc_method='.$rpcMethod.'&user_id='.$userEmail);

            if ($response->status() === 200 && ! isset($response['error'])) {
                return $response['response'][0]['getmemberbalance'][0]['member_balance'];
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

    }

    public function getSsoRequest()
    {

        $customer = customer(true);
        $rpcMethod = 'sso';
        $rpcExpiration = now()->addHours(24)->format('YmdHis');
        $userId = $customer->email;
        $nameParts = explode(' ', $customer->name);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? $nameParts[0];
        $query = http_build_query([
            'rpc_method' => $rpcMethod,
            'user_id' => $userId,
            'rpc_expiration' => $rpcExpiration,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);
        $hmac = hash_hmac('md5', $query, $this->secret);

        return $this->baseUrl.$this->clientToken.'/'.$hmac.'/?'.$query;
    }
}

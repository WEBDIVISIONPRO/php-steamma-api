<?php
/**
 * Created by WEBDIVISION.PRO
 */
final class Steamma
{
    private $base = 'https://steamma.com/api/';
    private $apikey = '';

    public function __construct($apikey){
        $this->apikey = $apikey;
    }

    private function query($method, $request, $params = array()){
        $header = array('Apikey: '  . $this->apikey);
        $uri = $this->base . $method;
        if (!empty($params) && $request == 'GET') {
            $params = http_build_query($params);
            $uri = $uri . '?' . $params;
        }
        $ch = curl_init($uri);
        if ($request == 'POST' || $request == 'PUT' || $request == 'DELETE') {
            array_push($header, 'Content-Type: application/json');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($code == 429) {
            return array('success' => false, 'message' => 'Too many requests!', 'data' => array());
        }
        if ($code == 403) {
            return array('success' => false, 'message' => 'Your apikey is empty!', 'data' => array());
        }
        if ($code != 200) {
            return array('success' => false, 'message' => 'Undefined error! Please, write to support! Status ' . $code . '.', 'data' => array());
        }
        if (empty($result)) {
            return array('success' => false, 'message' => 'The result is empty!', 'data' => array());
        }
        return $result;
    }

    public function getMarketInfo(){
        return $this->query('market/info', 'GET');
    }

    public function getMarketSales($appid, $contextid, $sort, $search, $from, $to, $page, $name = '', $userid = ''){
        return $this->query('market/items/sales', 'GET', array('appid' => $appid, 'contextid' => $contextid, 'sort' => $sort, 'search' => $search, 'from' => $from, 'to' => $to, 'page' => $page, 'name' => $name, 'userid' => $userid));
    }

    public function getMarketPurchases($appid, $contextid, $sort, $search, $from, $to, $page, $name = '', $userid = ''){
        return $this->query('market/items/purchases', 'GET', array('appid' => $appid, 'contextid' => $contextid, 'sort' => $sort, 'search' => $search, 'from' => $from, 'to' => $to, 'page' => $page, 'name' => $name, 'userid' => $userid));
    }

    public function getMarketHistory($appid, $contextid, $sort, $search, $from, $to, $page, $name = ''){
        return $this->query('market/items/history', 'GET', array('appid' => $appid, 'contextid' => $contextid, 'sort' => $sort, 'search' => $search, 'from' => $from, 'to' => $to, 'page' => $page, 'name' => $name));
    }

    public function getPurchasesInfoForItems($items){
        return $this->query('market/items/info/purchases', 'POST', array( 'items' => $items));
    }

    public function getSalesInfoForItems($items){
        return $this->query('market/items/info/sales', 'POST', array('items' => $items));
    }

    public function getSteamInfoForItems($items){
        return $this->query('market/items/info/steam', 'POST', array('items' => $items));
    }

    public function inspectItems($items){
        return $this->query('market/items/inspect', 'POST', array('items' => $items));
    }

    public function getLinkForInspectItemInGame($id){
        return $this->query('market/item/inspect', 'GET', array('id' => $id));
    }

    public function getUserProfile($id){
        return $this->query('user', 'GET', array('id' => $id));
    }

    public function getUserAds($id){
        return $this->query('user/ads', 'GET', array('id' => $id));
    }

    public function getUserSells($id){
        return $this->query('user/sells', 'GET', array('id' => $id));
    }

    public function getUserBuys($id){
        return $this->query('user/buys', 'GET', array('id' => $id));
    }

    public function donate($id, $amount){
        return $this->query('market/donate', 'POST', array('id' => $id, 'amount' => $amount));
    }

    public function getUsers($sort, $search, $page){
        return $this->query('users', 'GET', array('sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function getUsersByIds($ids){
        return $this->query('users/ids', 'POST', array('ids' => $ids));
    }

    public function getProfile(){
        return $this->query('account/profile', 'GET');
    }

    public function reCreateApiKey($speed){
        return $this->query('account/profile/apikey/recreate', 'PUT', array('speed' => $speed));
    }

    public function enableReceiveEmail($isEnable){
        return $this->query('account/profile/receive/email', 'PUT', array('isEnable' => $isEnable));
    }

    public function setLang($lang){
        return $this->query('account/profile/lang', 'PUT', array('lang' => $lang));
    }

    public function setCurrency($currency){
        return $this->query('account/profile/currency', 'PUT', array('currency' => $currency));
    }

    public function getUserFavorites($sort, $search, $page){
        return $this->query('account/profile/favorites', 'GET', array('sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function getUserBlacklist($sort, $search, $page){
        return $this->query('account/profile/blacklist', 'GET', array('sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function addToUserFavorites($ids){
        return $this->query('account/profile/favorites/add', 'POST', array('ids' => $ids));
    }

    public function addToUserBlacklist($ids){
        return $this->query('account/profile/blacklist/add', 'POST', array('ids' => $ids));
    }

    public function deleteUserFavorites($ids){
        return $this->query('account/profile/favorites/delete', 'POST', array('ids' => $ids));
    }

    public function deleteUserBlacklist($ids){
        return $this->query('account/profile/blacklist/delete', 'POST', array('ids' => $ids));
    }

    public function getUserPromotion($sort, $search, $page){
        return $this->query('account/profile/promotion', 'GET', array('sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function createUserPromotion($lang, $text, $link, $isEnable){
        return $this->query('account/profile/promotion/create', 'POST', array('lang' => $lang, 'text' => $text, 'link' => $link, 'isEnable' => $isEnable));
    }

    public function editUserPromotion($id, $lang, $text, $link, $isEnable){
        return $this->query('account/profile/promotion/edit', 'PUT', array('id' => $id, 'lang' => $lang, 'text' => $text, 'link' => $link, 'isEnable' => $isEnable));
    }

    public function deleteUserPromotion($ids){
        return $this->query('account/profile/promotion/delete', 'POST', array('ids' => $ids));
    }

    public function getUserReports($sort, $search, $page){
        return $this->query('account/profile/reports', 'GET', array('sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function getNotifs(){
        return $this->query('account/profile/notifs', 'GET');
    }

    public function deleteNotif($id){
        return $this->query('account/profile/notifs/delete/id', 'DELETE', array('id' => $id));
    }

    public function deleteNotifs(){
        return $this->query('account/profile/notifs/delete/all', 'DELETE');
    }

    public function getAds($type, $currency, $payment, $sort, $onlyMy, $search, $page, $userid = ''){
        return $this->query('account/balance/ads', 'GET', array('type' => $type, 'currency' => $currency, 'payment' => $payment, 'sort' => $sort, 'onlyMy' => $onlyMy, 'search' => $search, 'page' => $page, 'userid' => $userid));
    }

    public function getAd($id){
        return $this->query('account/balance/ad', 'GET', array('id' => $id));
    }

    public function createAd($type, $amount, $currency, $price, $isAutoPrice, $payment, $timeLimit, $terms, $isEnable){
        return $this->query('account/balance/ads/create', 'POST', array('type' => $type, 'amount' => $amount, 'currency' => $currency, 'price' => $price, 'isAutoPrice' => $isAutoPrice, 'payment' => $payment, 'timeLimit' => $timeLimit, 'terms' => $terms, 'isEnable' => $isEnable));
    }

    public function editAd($id, $type, $amount, $currency, $price, $isAutoPrice, $payment, $timeLimit, $terms, $isEnable){
        return $this->query('account/balance/ads/edit', 'PUT', array('id' => $id, 'type' => $type, 'amount' => $amount, 'currency' => $currency, 'price' => $price, 'isAutoPrice' => $isAutoPrice, 'payment' => $payment, 'timeLimit' => $timeLimit, 'terms' => $terms, 'isEnable' => $isEnable));
    }

    public function enableAds($isEnable){
        return $this->query('account/balance/ads/enable/all', 'PUT', array('isEnable' => $isEnable));
    }

    public function deleteAd($id){
        return $this->query('account/balance/ads/delete/id', 'DELETE', array('id' => $id));
    }

    public function deleteAds(){
        return $this->query('account/balance/ads/delete/all', 'DELETE');
    }

    public function getExchanges($status, $sort, $search, $page){
        return $this->query('account/balance/exchanges', 'GET', array('status' => $status, 'sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function exchange($id, $message, $image, $created){
        return $this->query('account/balance/exchanges/create', 'POST', array('id' => $id, 'message' => $message, 'image' => $image, 'created' => $created));
    }

    public function getExchange($id){
        return $this->query('account/balance/exchange', 'GET', array('id' => $id));
    }

    public function sendExchangeMessage($id, $message, $image){
        return $this->query('account/balance/exchanges/message', 'POST', array('id' => $id, 'message' => $message, 'image' => $image));
    }

    public function sendExchangeReview($id, $isLiked, $review){
        return $this->query('account/balance/exchanges/review', 'POST', array('id' => $id, 'isLiked' => $isLiked, 'review' => $review));
    }

    public function markExchangeAsReceived($id){
        return $this->query('account/balance/exchanges/mark/received', 'PUT', array('id' => $id));
    }

    public function markExchangeAsGiven($id){
        return $this->query('account/balance/exchanges/mark/given', 'PUT', array('id' => $id));
    }

    public function markExchangeAsDispute($id){
        return $this->query('account/balance/exchanges/mark/dispute', 'PUT', array('id' => $id));
    }

    public function markExchangeAsCanceled($id){
        return $this->query('account/balance/exchanges/mark/canceled', 'DELETE', array('id' => $id));
    }

    public function getMyHistory($service, $sort, $search, $page){
        return $this->query('account/balance/history', 'GET', array('service' => $service, 'sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function loadSales($appid, $contextid){
        return $this->query('account/inventory/sales/load', 'GET', array('appid' => $appid, 'contextid' => $contextid));
    }

    public function getSales($appid, $contextid, $sort, $search, $page){
        return $this->query('account/inventory/sales', 'GET', array('appid' => $appid, 'contextid' => $contextid, 'sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function updateSales($items){
        return $this->query('account/inventory/sales/update', 'PUT', array('items' => $items));
    }

    public function sell($items){
        return $this->query('account/inventory/sales/sell', 'POST', array('items' => $items));
    }

    public function deleteSales($appid, $contextid, $items){
        return $this->query('account/inventory/sales/delete', 'DELETE', array('appid' => $appid, 'contextid' => $contextid, 'items' => $items));
    }

    public function getPurchases($appid, $contextid, $sort, $search, $page){
        return $this->query('account/inventory/purchases', 'GET', array('appid' => $appid, 'contextid' => $contextid, 'sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function addToPurchases($items){
        return $this->query('account/inventory/purchases/add', 'POST', array('items' => $items));
    }

    public function updatePurchases($items){
        return $this->query('account/inventory/purchases/update', 'PUT', array('items' => $items));
    }

    public function buy($items){
        return $this->query('account/inventory/purchases/buy', 'POST', array('items' => $items));
    }

    public function deletePurchases($appid, $contextid, $items, $isSetZero){
        return $this->query('account/inventory/purchases/delete', 'DELETE', array('appid' => $appid, 'contextid' => $contextid, 'items' => $items, 'isSetZero' => $isSetZero));
    }

    public function getTrades($status, $sort, $search, $page){
        return $this->query('account/inventory/trades', 'GET', array('status' => $status, 'sort' => $sort, 'search' => $search, 'page' => $page));
    }

    public function getTrade($id){
        return $this->query('account/inventory/trade', 'GET', array('id' => $id));
    }

    public function sendTradeMessage($id, $message, $image){
        return $this->query('account/inventory/trades/message', 'POST', array('id' => $id, 'message' => $message, 'image' => $image));
    }

    public function sendTradeReview($id, $isLiked, $review){
        return $this->query('account/inventory/trades/review', 'POST', array('id' => $id, 'isLiked' => $isLiked, 'review' => $review));
    }

    public function markTradeAsCanceled($items){
        return $this->query('account/inventory/trades/mark/canceled', 'DELETE', array('items' => $items));
    }

}
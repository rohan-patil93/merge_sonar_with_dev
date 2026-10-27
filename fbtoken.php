<?php

//echo  __DIR__; exit;
require __DIR__.'/vendor/autoload.php';
//echo  __DIR__; exit;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

$serviceAccountPath = __DIR__ . '/infistaq-firebase-adminsdk-s10we-0ea8b831cd.json';

$factory = (new Factory)->withServiceAccount($serviceAccountPath);
$auth = $factory->createAuth();

// This token comes from frontend (e.g., Firebase JS SDK after login)
$idTokenString = "ya29.c.c0ASRK0GYGvM7UdIfD2nqfJERlpoTK2aSEVVUKXYQF0PT74jrxXDUwo0fJuOgfJmp_Hx8EgvjpDJPGw_J74AjwrXghtYNgKYKZOqkdWWoAZ8Eqa_xu-Rm7CJL8aq1JSvbAQd_-J-EIpdidSronEMb4SPdrif0tNB9HLmDtCue7BW-LgUGGwUcFGiW-hx0WQ-s3o9ZSz49QdOI2nehmXN8k_WWLX-XXy6gNhEk5j1-hnTd9IdsUrppULtdf1MFBSO1izYLgoloNXGPUCKQP62N1UbY0jL7sogDku4nwTKdkm8KpeaGkxAH6avfHKtmrURwMEkRnUMuYq7CJI1ERIpSdmjZQlvvh8PZsDT9FAqf2PfbwBZi3weU3hUMr5f2fL389DskJkVva4XIOW5-6MnJWwMedUnwm05hrmr5F3mUiJ9Jg8-7USopVQJan1vuidk-tJviZs9YdMOMmfca9gOq90njuX7MqQQIjbdFvI2zQga0hUhQ04olfJrt8Ozx5S0m-FFV5sihoMYImZBdU45uRORJuve18rJ9SJ6Qz_0sRS3ivqwhp1x_Vxgbl-Z52OW4vekppbsJea5rfvgBtRzUXQvbxOyaBQnJOY710hvh31uSrJm3IBeOYb1ml6VktB5rSX8g-XgySB_ym1SwwxXIdh4VwrJFOf8l5jMSgerItzB-bZ6u9jplafoxX8XeVFJQ3sWxUw01ji2JS2mvptnO8iRbd9J2lyRdhWfIh7haImbJlBZpcSQzXj6OkpZ_1g2f0i9o_I8Fq_Ib_FIs8n7vJ6p7tc1BaIIQb8p_Wm43mpnIelqFlOfXjM_VFUt-RrFgdc2xfdBpQUxVbaq2VUO8jugVRnVZzabx49nuOg8dmlllWn-f8wZ4FwOpl0a3QFs6QgaQktwFIpWtzXppzhB4inM7Ymv-Z_5wZrOIihmXWYxv7gZx54oR36ixJqopYo5Skz43gjsy34lbQ8qmSkWvgef5iqXsY-h2JW65j522--webQ1-ibYyX6Wr";

try {
    $verifiedIdToken = $auth->verifyIdToken($idTokenString);
    $uid = $verifiedIdToken->claims()->get('sub');

    echo "User ID: " . $uid;
} catch (\Kreait\Firebase\Exception\Auth\FailedToVerifyToken $e) {
    echo "Invalid token: " . $e->getMessage();
}
?>
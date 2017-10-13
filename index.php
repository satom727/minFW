<?php
//require_once( dirname(__FILE__).'/app/config/define.php' );
// 末尾の/を削除
$param = preg_replace( '/\/$/', '', $_SERVER['REQUEST_URI'] );
// queryを削除
$param = strtolower( preg_replace( '/\/?$/', '', $param ) );
// application root以下のpathのみ取得
$lastPos = strrpos($param,dirname(__FILE__));
$param = mb_substr($param,$lastPos);
// .を削除
$param = preg_replace( '/./', '', $param );
$params = array();
if ( !empty( $param ) ) {
    // パラメーターを / で分割
    $params = explode( '/', $param );
}

 
// 1番目のパラメーターをコントローラーとして取得
$controller = ( !empty( $params[0] ) ) ? $params[0] : 'index';
// 2番目のパラメータをメソッドとして取得
$methodName = ( !empty( $params[1] ) ) ? $params[1] : 'index';
// パラメータより取得したコントローラー名によりクラス振分け
$controllerFile = $controller.'_controller.php';
$controllerClass = ucfirst( $controller ).'Controller';
// モデルも読み込む
$modelFile = $controller.'.php';
 
if ( !is_file( APPPATH.'/controllers/'.$controllerFile ) ) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
// コントローラーファイル読込
require_once( APPPATH.'/controllers/'.$controllerFile );
if ( is_file( APPPATH.'/models/'.$modelFile ) ) {
    require_once( APPPATH.'/models/'.$modelFile );
}
// インスタンス化したいクラスを引数で渡す
$class = new ReflectionClass( $controllerClass );
// インスタンス化
$obj = $class->newInstance();
// メソッドを取得
if ( !$class->hasMethod( $methodName ) ) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
$method = $class->getMethod( $methodName );
// メソッドを起動
$method->invoke($obj);

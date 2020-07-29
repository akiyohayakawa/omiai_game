<?

ini_set('log_errors' , 'on');
ini_set('error_log','php.log');
session_start();

$man = array();

abstract class Human{
  protected $name;
  protected $status;
  abstract public function say();

  public function getName(){
    return $this->name;
  }
  public function setStatus($num){
    $this->status = $num;
  }
  public function getStatus(){
    return $this->status;
  }
 }

 class Woman extends Human{
  public function __construct($name, $status) {
    $this->name = $name;
    $this->status = $status;
  }
  public function like($target){
    $likePoint = 10;
    $target->setStatus($target->getStatus() + $likePoint);
    History::set($likePoint.'ポイントつけた');
  }
  public function love($target){
    $lovePoint = 30;
    $target->setStatus($target->getStatus() + $lovePoint);
    History::set($lovePoint.'ポイントつけた');
  }
  public function say(){
    History::set($this->name.'：「やったー♪」');
  }
}

class Man extends Human{
  protected $img;
  protected $likeMin;
  protected $likeMax;

  public function __construct($name, $status, $img, $likeMin, $likeMax) {
    $this->name =$name;
    $this->status =$status;
    $this->img =$img;
    $this->likeMin =$likeMin;
    $this->likeMax =$likeMax;
  }
  public function getImg(){
    return $this->img;
  }
  public function say(){
    History::set($this->name.'：「よっしゃ！」');
  }
  public function likeMan($target){
    $likePoint = mt_rand($this->likeMin, $this->likeMax);
    if(!mt_rand(0,9)){
      $likePoint = $likePoint * 1.5;
      $likePoint = (int)$likePoint;
      History::set($this->getName().'が「すごく好き」ポイントをつけた');
    }
    $target->setStatus($target->getStatus() + $likePoint);
  }
}

interface HistoryInterface{
  public static function set($str);
  public static function clear();
}
class History implements HistoryInterface{
  public static function set($str){
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}

$woman = new Woman('あき', 100);

$man[] = new Man('ジェイソン・ステイサム', 50, 'img/577694.png', 20, 60);
$man[] = new Man('ブラッド・ピット', 30, 'img/705008.png', 10, 60);
$man[] = new Man('ラッセル・クロウ', 20, 'img/2002328.png', 10, 40);
$man[] = new Man('ユアン・マクレガー', 20, 'img/2059976.png', 20, 90);
$man[] = new Man('レオナルド・ディカプリオ', 10, 'img/2090797.png', 20, 40);
$man[] = new Man('トニー・レオン', 10, 'img/844186.png', 10, 40);

function createMan(){
  global $man;
  $mens = $man[mt_rand(0,5)];
  History::set($mens->getName().'が登場');
  $_SESSION['mens'] = $mens;
}
function createWoman(){
  global $woman;
  $_SESSION['woman'] = $woman;
  }
function init(){
  History::clear();
  History::set('他をあたります');
  // $_SESSION['KnockDownCount'] = 0;
  createWoman();
  createMan();
}
function gameOver(){
  $_SESSION = array();
}


if(!empty($_POST)){
  $likeFlg = (!empty($_POST[('like')])) ? true : false;
  $loveFlg = (!empty($_POST['love'])) ? true : false;
  $startFlg = (!empty($_POST['start'])) ? true : false;
  error_log('POSTあり');

  if($startFlg){
    History::set('ゲームスタート');
    init();
  }else{
    if($likeFlg){
      History::set($_SESSION['woman']->getName().' の好きアプローチ');
      $_SESSION['woman']->like($_SESSION['mens']);
      $_SESSION['mens']->say();

      History::set($_SESSION['mens']->getName().' のアプローチ');
      $_SESSION['mens']->likeMan($_SESSION['woman']);
      $_SESSION['woman']->say();
  }else{
    if($loveFlg){
      History::set($_SESSION['woman']->getName().' のすごく好きアプローチ');
      $_SESSION['woman']->love($_SESSION['mens']);
      $_SESSION['mens']->say();

      History::set($_SESSION['mens']->getName().' のアプローチ');
      $_SESSION['mens']->likeMan($_SESSION['woman']);
      $_SESSION['woman']->say();
      }

      if($_SESSION['woman']->getStatus() >= 400){
        gameOver();
      }else{
        if($_SESSION['mens']->getStatus() >= 100){
          History::set($_SESSION['mens']->getName().' は去った');
          createMan();
          // $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;

    }else{
      History::set('この人は嫌！');
      createMan();
    }
  }
  $_POST = array();

}
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>お見合いゲーム</title>
</head>
<body>
  <h1 >お見合いゲーム</h1>
    <div class="main">
      <? if(empty($_SESSION)){ ?>
        <h2>いいヒト探します？</h2>
        <form method="post">
          <input type="submit" name="start" value="▶お見合いスタート！">
        </form>
      <? }else{ ?>
        <h2><? echo $_SESSION['mens']->getName(). 'とかどう？'; ?></h2>
        <div class="man-img">
          <img src="<? echo $_SESSION['mens']->getImg(); ?>" alt="">
        </div>
        <p class="man-status">お相手のステータス：<? echo $_SESSION['mens']->getStatus(); ?></p>
        <!-- <p>お見合いした人数：<?php echo $_SESSION['knockDownCount']; ?></p> -->
        <p>自分のステータス：<? echo $_SESSION['woman']->getStatus(); ?></p>

        <form method="post">
          <input type="submit" name="like" value="▶好き♡">
          <input type="submit" name="love" value="▶すごく好き♡">
          <input type="submit" name="dislike" value="▶好みじゃない">
          <input type="submit" name="start" value="▶他をあたる">
        </form>
      <? } ?>
      <div class="history">
        <p><? echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
      </div>
    </div>
</body>
</html>

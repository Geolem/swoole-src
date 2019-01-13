--TEST--
swoole_redis_coro: redis subscribe multi
--SKIPIF--
<?php require __DIR__ . '/../include/skipif.inc'; ?>
--FILE--
<?php
require __DIR__ . '/../include/bootstrap.php';

go(function () {
    $redis = new Co\Redis();
    $redis->connect(REDIS_SERVER_HOST, REDIS_SERVER_PORT);

    $val = $redis->subscribe(['test1', 'test2', 'test3']);
    assert($val[0][0] == 'subscribe' && $val[1][0] == 'subscribe' && $val[2][0] == 'subscribe');
    assert($val[0][1] == 'test1' && $val[1][1] == 'test2' && $val[2][1] == 'test3');

    for ($i = 0; $i < 3; $i++) {
        $val = $redis->recv();
        assert($val and $val[0] == 'message');
    }

    $redis->close();
});

go(function () {
    $redis = new Co\redis;
    $redis->connect(REDIS_SERVER_HOST, REDIS_SERVER_PORT);
    co::sleep(0.1);

    $ret = $redis->publish('test1', 'hello');
    assert($ret);

    $ret = $redis->publish('test2', 'hello');
    assert($ret);

    $ret = $redis->publish('test3', 'hello');
    assert($ret);
});

?>
--EXPECT--

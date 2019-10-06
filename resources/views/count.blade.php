<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>统计页面</title>
</head>
<body>
<p>报名人数: {{ $apply_count }}</p>
<p>队伍总数: {{ $team_count }}</p>
<p>屏峰达到要求队伍数: {{ $upToTeam }}</p>
<p>朝晖达到要求队伍数: {{ $ch }}</p>
<button ><a href="{{ route('user') }}">获取用户表格</a> </button>
<button><a href="{{ route('group') }}">获取队伍表格</a> </button>

</body>
</html>
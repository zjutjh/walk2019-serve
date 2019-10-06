<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>信息发送</title>
</head>
<body>
<form action="{{ route('send') }}" method="post" style="display: block;">
    <label for="title">发送标题</label>
    <input type="text" id="title" name="title">
    <label for="content">发送内容</label>
    <textarea id="content" name="content" rows="5"></textarea>
    <label for="ids">发送id</label>
    <textarea id="ids" name="ids" rows="15"></textarea>
    <input type="submit">
</form>

</body>
</html>
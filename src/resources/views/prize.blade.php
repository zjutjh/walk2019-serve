<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>精弘毅行-奖池</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .msg {
            color: red;
        }

        .title {
            font-size: 84px;
        }

        .campus {
            font-size: 48px;
        }

        .captain {
            font-size: 32px;
        }



        .prize-count {
            font-size: 24px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 56px;
        }

        .decorate {
            color: indianred;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div>
            <form method="post">
                <div>
                    <label for="input-no">请输入要抽奖的队伍编号：</label></label><input id="input-no" type="text" name="no"/>
                </div>
                <div>
                    <input type="submit" value="抽奖">
                </div>
                {{ csrf_field() }}
            </form>
        </div>
        <div>
            @if($result ?? '')
                <div><span class="msg">{{$result['msg']}}</span></div>
                @if(array_key_exists('data',$result))
                    <div><span class="captain">{{ $result['no'] }} - {{$result['data']['captain']}}</span></div>
                    <div>
                        <span>{{$result['data']['content']}}</span>
                    </div>
                @endif
            @endif
        </div>
        <div>
            @foreach ($prize_pool as $prize)
            <span class="campus">{{ $prize['campus'] }}</span>
                @foreach ($prize['data'] as $item)
                    <div>
                <span >{{ $item['captain'] }}: </span>
                <span >{{ $item['content'] }} </span>
                <span class="prize-count">{{ $item['count'] }}/{{ $item['capacity'] }} </span>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
</body>
</html>

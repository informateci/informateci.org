<?php
	$GLOBALS['fc_config']['languages']['kr'] = array(
		'name' => "한국어",

		'messages' => array(
			'ignored' => "'USER_LABEL'님은 대화에 응하지 않습니다.",
			'banned' => "이용 권한이 없습니다. 회원가입을 해 주십시오.",
			'login' => '로그인이 필요 합니다.',
			'wrongPass' => '아이디 혹은 비밀번호가 정확하지 않습니다. 다시한번 시도해 주십시오.',
			'required' => '요청',
			'anotherlogin' => '이 아이디는 다른 회원이 사용중입니다. 다시한번 시도해 주십시오.',
			'expiredlogin' => '접속이 끊어졌습니다. 다시 로그인해 주십시오.',
			'enterroom' => '[ROOM_LABEL]: USER_LABEL 님이 입장했습니다.(TIMESTAMP)',
			'leaveroom' => '[ROOM_LABEL]: USER_LABEL 님이 퇴장했습니다.(TIMESTAMP)',
			'selfenterroom' => '[ROOM_LABEL]: TIMESTAMP 입장',
			'bellrang' => 'USER_LABEL 님이 벨을 눌러 대화 상대방을 호출 합니다.',
			'chatfull' => '채팅 인원이 초과되었습니다. 다음에 이용해 주십시오.',
			'iplimit' => '이미 채팅중입니다.',
			'roomlock' => '이 방은 비공개룸입니다.<br>비밀번호를 입력해 주십시오:',
			'locked' => '비밀번호가 정확하지 않습니다. 다시 입력해 주십시오.',
			'botfeat' => '보트기능이 꺼져 있습니다.',
			'securityrisk' => '업로드한 파일에 보안에 영향을 미치는 스크립트가 포함되어 있습니다.<br />다른 파일을 업로드해 주십시오.',
		),

		'usermenu' => array(
			'profile' => '개인정보',
			'unban' => '차단해제',
			'ban' => '차단',
			'unignore' => '무시해제',
			'fileshare' => '파일공유',
			'ignore' => '무시',
			'invite' => '초대',
			'privatemessage' => '귓속말',			
		),

		'status' => array(
			'here' => '대화중',
			'busy' => '바쁨',
			'away' => '자리비움',
			'brb'  => '비공개 대화',			
		),

		'dialog' => array(
			'misc' => array(
				'roomnotfound' => "'ROOM_LABEL'방은 존재하지 않습니다.",
				'usernotfound' => "'USER_LABEL'님을 찾을 수 없습니다.",
				'unbanned' => "'USER_LABEL'님에 의해 차단이 해제 되었습니다.",
				'banned' => "'USER_LABEL'님에 의해 차단 되었습니다.",
				'unignored' => "'USER_LABEL'님이 바쁜상황을 해제 하였습니다.",
				'ignored' => "'USER_LABEL'님은 바쁜상황 입니다.",
				'invitationdeclined' => "'USER_LABEL'님이 'ROOM_LABEL'방에 초대한 것을 거절 하였습니다.",
				'invitationaccepted' => "'USER_LABEL'님이 'ROOM_LABEL'방으로 초대 하였습니다.",
				'roomnotcreated' => '방이 개설되지 않았습니다.',
				'roomisfull' => '[ROOM_LABEL]방의 인원이 초과되었습니다. 다른 방을 이용해 주십시오.',
				'alert' => '<b>경고!</b><br>',
				'chatalert' => '<b>경고!</b><br>',
				'gag' => "<b>회원께서는 DURATION 분 동안 대화를 할 수 없습니다.!</b><br><br>대화 내용을 볼 수는 있지만 참여할 수는 없습니다.".
						 "대화중지 시간이 끝난 후 대화를 시작할 수 있습니다.",
				'ungagged' => "'USER_LABEL'님이 대화중지를 해제하였습니다.",		 
				'gagconfirm' => 'USER_LABEL 님은 MINUTES 분 동안 대화가 중지되었습니다..',
				'alertconfirm' => 'USER_LABEL 님이 경고를 읽었습니다.',
				'file_declined' => '전송된 파일이 USER_LABEL 님에 의해 거절되었습니다.',
				'file_accepted' => 'USER_LABEL 님이 파일을 받았습니다.',
			),

			'unignore' => array(
				'unignoreBtn' => '바쁜상황 해제',
				'unignoretext' => '바쁜상황 내용 입력',
			),

			'unban' => array(
				'unbanBtn' => '차단해제',
				'unbantext' => '차단해제 내용 입력',
			),
			
			'tablabels' => array(
				'themes' => '테마',
				'sounds' => '소리',
				'text'  => '언어/서체',
				'effects'  => '효과',
				'admin'  => '관리자',
				'about' => '소개',
			),

			'text' => array(
				'itemChange' => '아이템 변경',
				'fontSize' => '글자크기',
				'fontFamily' => '서체',
				'language' => '언어',
				'mainChat' => '메인 채팅',
				'interfaceElements' => '인터페이스 요소',
				'title' => '제목',
				'mytextcolor' => '받은 모든 내용에 내 서체컬러 적용',
			),
			
			'effects' => array(
				'avatars' => '아바타',
				'photo' => '포토',
				'mainchat' => '메인 채팅',
				'roomlist' => '채팅룸 목록',
				'background' => '배경',
				'custom' => '사용자 설정',
				'showBackgroundImages' => '배경 보기',
				'splashWindow' => '새 메시지 창을 중심으로 표시',
				'uiAlpha' => '투명도 조정',
			),

			'sound' => array(
				'sampleBtn' => '샘플',
				'testBtn' => '테스트',
				'muteall' => '소리 끔',
				'submitmessage' => '메시지 전송',
				'reveivemessage' => '메시지 받음',
				'enterroom' => '입장',
				'leaveroom' => '퇴장',
				'pan' => '밸런스',
				'volume' => '볼륨',
				'initiallogin' => '로그인',
				'logout' => '로그아웃',
				'privatemessagereceived' => '귓속말 도착',
				'invitationreceived' => '초대장 도착',
				'combolistopenclose' => "콤보박스 목록 열고 닫음",
				'userbannedbooted' => '회원 차단 혹은 붐업',
				'usermenumouseover' => '사용자 메뉴 마우스 오버',
				'roomopenclose' => "채팅방 열고 닫음",
				'popupwindowopen' => '팝업창 염 ',
				'popupwindowclosemin' => '팝업창 닫음',
				'pressbutton' => '키 프레스',
				'otheruserenters' => '회원 입장',
			),

			'skin' => array(
				'inputBoxBackground' => '박스 배경 입력',
				'privateLogBackground' => '개인로그 배경',
				'publicLogBackground' => '공용로그 배경',
				'enterRoomNotify' => '입장 알림',
				'roomText' => '채팅방 서체',
				'room' => '채팅방 배경',
				'userListBackground' => '회원 목록 배경',
				'dialogTitle' => '다이얼로그 제목',
				'dialog' => '다이얼로그 배경',
				'buttonText' => '버튼 서체',
				'button' => '버튼 배경',
				'bodyText' => '본문 서체',
				'background' => '메인 배경',
				'borderColor' => '테두리 컬러',
				'selectskin' => '컬러느낌 선택...',
				'buttonBorder' => '버튼 테두리 컬러',
				'selectBigSkin' => '스킨 선택...',
				'titleText' => '제목 서체',
			),

			'privateBox' => array(
				'sendBtn' => '전송',
				'toUser' => 'USER_LABEL 님에게 귓속말:',
			),

			'login' => array(
				'loginBtn' => '로그인',
				'language' => '언어:',
				'moderator' => '(if moderator)',
				'password' => '비밀번호:',
				'username' => '아이디:',
			),

			'invitenotify' => array(
				'declineBtn' => '거절',
				'acceptBtn' => '승인',
				'userinvited' => "'USER_LABEL'님이 'ROOM_LABEL'방으로 초대하였습니다.",
			),

			'invite' => array(
				'sendBtn' => '전송',
				'includemessage' => '초대내용:',
				'inviteto' => '초대공간:',
			),

			'ignore' => array(
				'ignoreBtn' => '바쁜상황',
				'ignoretext' => '거절내용 입력',
			),

			'createroom' => array(
				'createBtn' => '개설',
				'private' => '비공개',
				'public' => '공개',
				'entername' => '대화방명',
				'enterpass' => '* 비공개를 선택하셨다면,
아래 빈 칸에 비밀번호를 입력하십시오.
입력하지 않을 경우 공개로 적용됩니다.',
			),

			'ban' => array(
				'banBtn' => '차단',
				'byIP' => 'IP',
				'fromChat' => '채팅내용',
				'fromRoom' => '방 제목',
				'banText' => '차단내용 입력',
			),

			'common' => array(
				'cancelBtn' => '취소',
				'okBtn' => '승인',
				
				'win_choose'         => '업로드할 파일 선택:',
				'win_upl_btn'        => '  업로드  ',
				'upl_error'          => '파일 업로드 에러',
				'pls_select_file'    => '업로드할 파일을 선택해 주십시오.',
				'ext_not_allowed'    => 'FILE_EXT 확장자는 업로드할 수 없는 확장자 입니다.
다음 확장자의 파일을 선택해 주십시오.: ALLOWED_EXT',
				'size_too_big'       => '파일 업로드 용량을 초과하였습니다.
용량을 낮추어 다시 시도해 주십시오.',
			),
			
			'sharefile' => array(
				'chat_users'=> '[ 대화상대에게만 전송 ]',
				'all_users' => '[ 대화방 전체 회원에게 전송 ]',
				'file_info_size'  => '<br>최대 업로드 용량은 MAX_SIZE 입니다.',
				'file_info_ext' => '<br>승인된 파일 확장자: ALLOWED_EXT',
				'win_share_only'=>'파일공유형태선택',				
				'usr_message' => '<b>USER_LABEL 님이 파일을 보내셨습니다.</b><br><br>파일명: F_NAME<br>파일크기: F_SIZE',				
			),
			
			'loadavatarbg' => array(
				'win_title'  => '사용자 배경',
				'file_info'  => '업로드 가능한 파일은 non-progressive JPG, GIF , PNG 파일 입니다.',
				'use_label'  => '파일 용도:',
				'rb_mainchat_avatar' => '메인 채팅 아바타용',
				'rb_roomlist_avatar' => '채팅방 목록 아바타용',
				'rb_mc_rl_avatar'    => '메인 채팅과 채팅방 목록 아바타용',
				'rb_this_theme'      => '이 테마에만 적용하는 배경',
				'rb_all_themes'      => '모든 테마에 적용하는 배경',
			),
			
			'loadphoto' => array(
				'win_title'  => '사용자 포토',
				'file_info'  => '업로드 가능한 파일은 non-progressive JPG, GIF , PNG 파일 입니다.',
			),		
		),

		'desktop' => array(
			'invalidsettings' => '부정확한 설정',
			'selectsmile' => '스마일',
			'sendBtn' => '전송',
			'saveBtn' => '저장',
			'clearBtn' => '지움',
			'skinBtn' => '설정',
			'addRoomBtn' => '개설',
			'myStatus' => '나의 상태',
			'room' => '대화방',
			'welcome' => 'USER_LABEL 님 환영 합니다.',
			'ringTheBell' => '',
			'logOffBtn' => 'X',
			'helpBtn' => '?',
			'adminSign' => '',
		)
	);
?>

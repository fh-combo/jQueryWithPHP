<!DOCTYPE html>
<html manifest="xiaocai.appcache">
<head>
	<title>晓菜</title>
	<meta charset="utf8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
	<link rel="stylesheet" href="extension/bootstrap.min.css" />
	<script type="text/javascript" src="extension/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="extension/unslider.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="extension/buttons.css">
	<script type="text/javascript">
		/******************************页面访问记录栈******************************/
		//构造函数
		function StorageStack(current,prev){
			this.currentPage=current;
			this.pageVisitedCount=0;
			this.prevPage=new Array();
			this.prevPage[this.pageVisitedCount]=prev;
		}
			//进栈
			StorageStack.prototype.push=function(val){
				this.pageVisitedCount+=1;
				this.prevPage[this.pageVisitedCount]=val;
			}
			//出栈
			StorageStack.prototype.pop=function(){
				if(this.pageVisitedCount!=0){
					return this.prevPage[this.pageVisitedCount--];
				}else{
					return null;
				}
			
			}
			//取栈顶
			StorageStack.prototype.top=function(i){
				return this.prevPage[i];
			}
			//是否空
			StorageStack.prototype.isEmpty=function(){
				return this.pageVisitedCount==0 ? true:false;
			}
			//更改当前页面
			StorageStack.prototype.changeCurrentPage=function(current){
				this.currentPage=current;
			}

			StorageStack.prototype.forEach=function(f){
				for(var i=0;i<=this.pageVisitedCount;i++){
					f(this.prevPage[i]);
				}
			}

			StorageStack.prototype.toString=function(){
				return JSON.stringify(this);
			}

		//将JSON数据转换为栈
		function JSON2Stack(o){
			var stackObj=JSON.parse(o);
			var lsObj=new StorageStack(stackObj.currentPage,stackObj.prevPage[0]);
			lsObj.pageVisitedCount=stackObj.pageVisitedCount;
			for(var i=1;i<=stackObj.pageVisitedCount;i++){
				lsObj.push(stackObj.prevPage[i]);
			}
			return lsObj;
		}
		/******************************页面访问记录栈******************************/

		/**********************************函数库**********************************/
		
		//当前页面可否滚动,在加载页面和弹出右侧工具栏的时候禁止滚动,默认可滚动
		var docIsMoved=1;
		//控制页面是否可滚动
		function setNoTouchMove(){docIsMoved=0;}
		function setTouchMove(){docIsMoved=1;}

		//返回上一个页面
		function backPreviosPage(currentPage){
			$('.loading').fadeIn();
			setNoTouchMove();
			var stackifyJSONStack=JSON2Stack(localStorage.pageStack);
			var pageLoaded=stackifyJSONStack.pop();
			$('body').load(pageLoaded,function(){
				$('.loading').fadeOut();
				setTouchMove();
				stackifyJSONStack.pageVisitedCount-=1;
				stackifyJSONStack.currentPage=pageLoaded;
				localStorage.pageStack=stackifyJSONStack;//更新localStorage
			});	
		}

		//加载新页面,pageName为要加载的页面名,elem为存放元素
		function loadPagesA(pageName,elem){
			$('.loading').fadeIn();
			setNoTouchMove();
			$(elem).load(pageName,function(){
				$('.loading').fadeOut();
				setTouchMove();
				var stackifyJSONStack=JSON2Stack(localStorage.pageStack);
				stackifyJSONStack.pageVisitedCount+=1;
				stackifyJSONStack.push(stackifyJSONStack.currentPage);
				stackifyJSONStack.currentPage=pageName;
				localStorage.pageStack=stackifyJSONStack;
			});
		}

		//显示信息提示框
		function displayALertForm(text,timeInterval){
			timeInterval=timeInterval==null ? 1000:timeInterval;
			var alertForm="<div class=\"alert-form\"></div>";
			$('body').append(alertForm);
			$('.alert-form').html(text);
			$('.alert-form').fadeIn();
			setTimeout(function(){
				$('.alert-form').fadeOut();
			},timeInterval);
		}

		//显示没有数据的提示,如果text内容为空则隐藏提示
		function displayNoData(text){
			var nodata="<div class=\"nodata-form\">"+text+"</div>";
			if(text==null){
				if($('.nodata-form').length>0){
					$('.nodata-form').remove();
				}
			}else{
				$('body').append(nodata);
			}
			
		}

		function checkMobile(sMobile){
    		if(!(/^1[3|4|5|8][0-9]\d{4,8}$/.test(sMobile))){
        		return false;
    		}else{
    			return true;
    		}
		}

		function inputInfoIsNull(elem){
			var flag=0;
			$(elem).each(function(index,element){
				if($(element).find('input').val()==''){
					flag+=1;
				}
			});
			return flag===0;
		}

		function hideShareForm(){
			$('.monoshare').fadeOut(function(){
				$('.monoshare').remove();
			});
			docIsMoved=1;
		}

		function displayShareForm(){
			var dom='<div onclick="hideShareForm()" class="monoshare"><div class="monoshareDiv"><ul id="line"><li id="shareTofriend"><img src=""></li><li id="shareTocircle"><img src=""></li></ul><ul id="monoshare-content"><li>发送给朋友</li><li>分享至朋友圈</li></ul></div></div>';
			$('body').append(dom);
			$('.monoshare').fadeIn();
			var leftRate=($('.monoshareDiv').width()/$(document).width()-10).toFixed(8);
			leftRate=leftRate.slice(2,4)+"."+leftRate.slice(4,8)+"%";
			$('.monoshareDiv').css('left',leftRate);
			docIsMoved=0;
		}

		function getCurrentTime(){
	        var myDate = new Date();
	        var month=myDate.getMonth()+1;
	        var date=myDate.getDate();
	        month=(month<10)?'0'+month:month;
	        date=(date<10)?'0'+date:date;
	        return myDate.getFullYear()+'-'+month+'-'+date;
      	}

      	function formatDate(date){
      		var nowadays=getCurrentTime();
			var LSTR_ndate=nowadays.split('-');
			var LSTR_Year=LSTR_ndate[0]; 
			var LSTR_Month=LSTR_ndate[1]; 
			var LSTR_Date=LSTR_ndate[2];
			var uom = new Date(LSTR_Year,LSTR_Month,LSTR_Date);
			uom.setDate(uom.getDate()-1);
			var LINT_MM=uom.getMonth(); 
			LINT_MM++; 
			var LSTR_MM=LINT_MM > 10?LINT_MM:("0"+LINT_MM);
			var LINT_DD=uom.getDate();
			var LSTR_DD=LINT_DD > 10?LINT_DD:("0"+LINT_DD);
			uom = uom.getFullYear() + "-" + LSTR_MM + "-" + LSTR_DD; 
			var globalDateList=[getCurrentTime(),uom];
			if(date==globalDateList[0]){
				return '今天';
			}else if(date==globalDateList[1]){
				return '昨天';
			}else{
				return date;
			}
      	}

      	function isUserAtBottom(){return ($(window).height()+$(window).scrollTop())>=$(document).height();}

		/*********************************AJAX请求*********************************/

		var rootURL="curl/";

		function registerOrLoginByWechat(p_openid,p_nickname,p_headimgurl,callback){
			$.post(
				rootURL+"weixin.php",
				{
					openid:p_mobile,
					nickname:p_nickname,
					headimgurl:p_headimgurl
				},callback);
		}

		/**
		* 通过手机号注册用户
		* @param mobile 手机号
		* @param password 密码
		* @param repassword 确认密码
		* @param code 手机验证码
		* @return JSONObject [uid|mobile|token_id]
		**/

		function regByMobile(p_mobile,p_password,p_repassword,p_code,callback){
			$.post(
				rootURL+"regbymobile.php",
				{
					mobile:p_mobile,
					password:p_password,
					repassword:p_repassword,
					code:p_code
				},callback);
		}

		/**
		* 通过手机号登录
		* @param mobile 手机号
		* @param password 密码
		* @return JSONObject [uid|nickname|is_reply(是否有回复留言 1有 0无)|headimgurl|token_id]
		*/

		function signInByMobile(p_mobile,p_password,callback){
			$.post(
				rootURL+"login.php",
				{
					mobile:p_mobile,
					password:p_password
				},callback);
		}

		/**
		* 注销登出
		* @param token_id 登录返回的token_id
		* @return Nothing
		*/

		function logOut(p_token_id,callback){
			$.post(
				rootURL+"logout.php",
				{token_id:p_token_id},
				callback);
		}

		/**
		* 修改昵称和头像
		* @param token_id 登录返回的token_id
		* @param nickname 新昵称
		* @param headimgurl 二进制文件头像
		* @return JSONObject [uid|nickname|headimgurl|token_id]
		*/

		function changeUserData(p_token_id,p_nickname,p_headimgurl,callback){
			$.post(
				rootURL+"changedata.php",
				{
					token_id:p_token_id,
					nickname:p_nickname,
					headimgurl:p_headimgurl
				},callback);
		}

		/**
		* @param mobile 手机号
		* @param type 发送类型 1 注册 2 忘记密码
		* @return Nothing
		*/

		function sendSms(p_mobile,p_type,callback){
			$.post(
				rootURL+"sendsms.php",
				{
					mobile:p_mobile,
					type:p_type
				},callback);
		}

		/**
		* 找回密码
		* @param mobile 手机号
		* @param password 密码
		* @param repassword 确认密码
		* @param code 手机验证码
		* @return JSONObject
		*/

		function forgotPassword(p_mobile,p_password,p_repassword,p_code,callback){
			$.post(
				rootURL+"forgotpassword.php",
				{
					mobile:p_mobile,
					password:p_password,
					repassword:p_repassword,
					code:p_code
				},callback);
		}

		/**
		* 修改密码
		* @param mobile 手机号
		* @param password 密码
		* @param repassword 确认密码
		* @param oldpassword 旧密码
		* @return JSONObject
		*/

		function changePassword(p_token_id,p_password,p_repassword,p_oldpassword,callback){
			$.post(
				rootURL+"changepassword.php",
				{
					token_id:p_token_id,
					password:p_repassword,
					repassword:p_repassword,
					oldpassword:p_oldpassword
				},callback);
		}

		/**
		* 关于页面
		* @return About
		*/

		function getAbout(callback){
			$.post(rootURL+"about.php",{},callback);
		}

		/**
		* 留言列表
		* @param token_id
		* @return JSONObject
		* @return article_image 留言的文章的图片地址
		* @return article_title 留言的文章的标题
		* @return content 留言内容
		* @return type 模块类型 1一手好菜 2玩转厨房 3首页文章 4专题
		* @return status 状态0正常 1后台回复未读 2用户已读
		* @return created_time 留言时间
		*/

		function getReply(p_token_id,callback){
			$.post(
				rootURL+"reply.php",
				{token_id:p_token_id},
				callback);
		}

     	/**
	  	* @param nil
     	* @return id
     	* @return image
     	* @return title
     	* @return type
     	* @return id
     	* @return title
     	* @return papaer
     	* @return browse_num
     	* @return title
     	* @return created_time
     	* @return video_id
     	* @return video_url_360
     	* @return video_url_480video_url_720
     	* @return video_url_1080
     	* @return arrange_image_url
     	* @return is_vip
     	* @return image
     	* @return big
     	* @return image
     	* @return small_image
     	*/

      	function getHome(p_page,p_limit,callback){
          	$.post(
                rootURL+'home.php',
            	{
            		page:p_page,
            		limit:p_limit
            	},callback);
     	}
     
     	/**
     	* @param nil
     	* @return id
     	* @return title
     	* @return icon
     	* @return childern
     	*/

      	function getRecipeClassify(callback){
          	$.post(
                rootURL+'recipeclassify.php',
              	{},callback);
     	}
     
     	/**
     	* @param id
     	* @return id
     	* @return title
     	* @return paper
     	* @return browse_num
     	* @return title
     	* @return created_time
     	* @return video_id
     	* @return video_url_360
     	* @return video_url_480video_url_720
     	* @return video_url_1080
     	* @return arrange_image_url
     	* @return is_vip
     	* @return image
     	*/

	    function getRecipeList(p_id,p_page,p_limit,callback){
	        $.post(
	            rootURL+'recipelist.php',
	            {
	                id:p_id,
	                page:p_page,
	                limit:p_limit
	            },callback);
	    }
     
	    /**
	    * @param id
	    * @param comments_id
	    * @return id
	   	* @return title
	    * @return paper
	    * @return browse_num
     	* @return title
	    * @return created_time
	    * @return video_id
	    * @return video_url_360
	    * @return video_url_480video_url_720
	    * @return video_url_1080
	    * @return arrange_image_url
	    * @return prepare_time
	    * @return image
	    * @return cooking_time
	    * @return enjoy_num
	    * @return id
	    * @return user_id
	    * @return username
	    * @return content
	    * @return headimgurl
	    * @return created_time
	    * @return reply_username
	    * @return reply_content
	    * @return reply_time
	    */

	    function getRecipeInfo(p_id,p_comments_id,p_page,p_limit,callback){
	        $.post(
	            rootURL+'recipeinfo.php',
	            {
	                id:p_id,
	                comments_id:p_comments_id,
	                page:p_page,
	                limit:p_limit
	            },callback);
	    }

	    /**
	    * @param id
	  	* @return id
	    * @return content
	    * @return children
	    * @return id
	    * @return type
		* @return content
	    * @return tips
	    * @return recommened
	    */

	    function getRecipeInfoSteps(p_id,callback){
	        $.post(
	            rootURL+'recipeinfosteps.php',
	            {
	                id:p_id
	            },callback);
	    }

	    /**
	    * @param id
	  	* @return id
	    * @return content
	    * @return children
	    * @return id
	    * @return type
		* @return content
	    * @return messsage
	    * @return node
	    */

	    function getRecipeInfoFormula(p_id,callback){
	        $.post(
	            rootURL+'recipeinfoformula.php',
	            {
	                id:p_id
	            },callback);
	    }

	    /**
	    * @param nil
	    * @return id
	    * @return title
	    * @return paper
	    * @return browse
	    * @return num
	    * @return small_image
	    * @return big_image
	    * @return created_time
	    */

	    function getSkillsList(p_page,p_limit,callback){
	        $.post(
	            rootURL+'skillslist.php',
	            {
	            	page:p_page,
	            	limit:p_limit
	            },callback);
	    }

	    /**
	    * @param id
	    * @param comment_id
	    * @return id
	    * @return title
	  	* @return papaer
	    * @return content
	    * @return browse_num
	    * @return small_image
	    * @return big_image
	    * @return created_time
	    * @return id
	    * @return user_id
	    * @return username
	    * @return content
	    * @return headimgurl
	    * @return created_time
	    * @return reply_username
	    * @return reply_content
	    * @return reply_time
	    */

	    function getSkillsInfo(p_id,p_comment_id,p_page,p_limit,callback){
	        $.post(
	            rootURL+'skillsinfo.php',
	            {
	                id:p_id,
	                comments_id:p_comment_id,
	                page:p_page,
	                limit:p_limit
	            },callback);
	    }

	    /**
	    * @param type
	    * @param token_id
	    * @param article_id
	    * @param content
	    * @return Nothing
	    */

	    function sendComments(p_type,p_token_id,p_article_id,p_content,callback){
	        $.post(
	       	    rootURL+'comments.php',
	            {
	                type:p_type,
	                token_id:p_token_id,
	                article_id:p_article_id,
	                content:p_content
	            },callback);
	    }

	    function getHomeInfo(p_id,p_comments_id,p_page,p_limit,callback){
	    	$.post(
	            rootURL+'homeinfo.php',
	            {
	                id:p_id,
	                comments_id:p_comments_id,
	                page:p_page,
	                limit:p_limit
	            },callback);
	    }

	    function getProjectInfo(p_id,p_comments_id,p_page,p_limit,callback){
	    	$.post(
	            rootURL+'projectinfo.php',
	            {
	                id:p_id,
	                comments_id:p_comments_id,
	                page:p_page,
	                limit:p_limit
	            },callback);
	    }

	    function addReadingList(p_type,p_token_id,p_article_id,callback){
	    	$.post(
	            rootURL+'addreadinglist.php',
	            {
	                type:p_type,
	                token_id:p_token_id,
	                article_id:p_article_id
	            },callback);
	    }

	    function getReadingList(p_type,p_token_id,callback){
	    	$.post(
	            rootURL+'readinglist.php',
	            {
	                type:p_type,
	                token_id:p_token_id
	            },callback);
	    }

	    function addFoodList(p_recipe_id,p_formula_id,p_token_id,callback){
	    	$.post(
	            rootURL+'addfoodlist.php',
	            {
	                recipe_id:p_recipe_id,
	                formula_id:p_formula_id,
	                token_id:p_token_id
	            },callback);
	    }

	    //p_formula_id或p_recipe_id为0表示不传参,不能同时为0
	    function deleteFoodList(p_recipe_id,p_formula_id,p_token_id,callback){
	    	$.post(
	            rootURL+'deletefoodlist.php',
	            {
	                recipe_id:p_recipe_id,
	                formula_id:p_formula_id,
	                token_id:p_token_id
	            },callback);
	    }

	    function getFoodList(p_token_id,callback){
	    	$.post(
	            rootURL+'foodlist.php',
	            {
	                token_id:p_token_id
	            },callback);	
	    }

	    function search_(p_keyword,callback){
	    	$.post(
	            rootURL+'search.php',
	            {
	                keyword:p_keyword
	            },callback);	
	    }

		/*********************************AJAX请求*********************************/

		/**********************************函数库**********************************/

		/*********************************DOM操作**********************************/

		function changeReadingListSize(data){
			var changeFontSizeCSS='';
			var charCount=data.length;
			if(charCount>=20){
				changeFontSizeCSS="readling-list-title-small";
			}else{changeFontSizeCSS='';}
			return changeFontSizeCSS;
		}

		function cutReadingListPaper(data){
			var paperContent=data;
			var charCount=paperContent.length;
			if(charCount>=20){
				return paperContent.substring(0,20)+'……';
			}
			return paperContent;
		}

		function cutReadingListTitle(data){
			var paperTitle=data;
			var titleCount=paperTitle.length;
			if(titleCount>=20){
				return paperTitle.substring(0,20)+'……';
			}
			return paperTitle;
		}

		function printReadingList(jsondata,elem){
			var homeList=jsondata;
			var homeListHtmlDOM='';
			for (var i = 0; i < homeList.length; i++) {
				var papaerContent=homeList[i]['paper'];
				var paperTitle=homeList[i]['title'];
				var changeFontSizeCSS;
				paperTitle=cutReadingListTitle(paperTitle);
				changeFontSizeCSS=changeReadingListSize(papaerContent);
				papaerContent=cutReadingListPaper(papaerContent);
				homeListHtmlDOM+='<div ref="monograph.php#'+homeList[i]['id']+'#type2" onclick="locateToIntroduction(this)" id="skills-'+homeList[i]['id']+'" class="reading-list-a"><div style="background:url('+homeList[i]['image']+') no-repeat scroll center center transparent;background-size:cover;" class="reading-list-img"></div><div class="reading-list-all-content"><div class="reading-list-all-title '+changeFontSizeCSS+'"><p>'+paperTitle+'</p></div><div class="reading-list-all-summary"><p>'+papaerContent+'</p></div></div><div class="reading-list-all-footer"><ul><li><span class="glyphicon glyphicon-bookmark"></span> 玩转厨房</li><li><span class="glyphicon glyphicon-time"></span> '+formatDate(homeList[i]['created_time'].split(' ')[0])+'</li></ul></div></div>';
			};
			$(elem).append(homeListHtmlDOM+'<div class="padding-div-row"></div>');
		}

		function addToShoppingList(obj){
			var this_=$(obj);
			recipedID=this_.attr('recipeid');
			formulaID=this_.attr('formulaid');
	        displayALertForm('正在为您加入采购清单...');
	        addFoodList(recipedID,formulaID,localStorage.tokenID,function(data){
	          if(data!=''){
	            var jsonData=JSON.parse(data);
	            displayALertForm(jsonData['msg']);
	          }else{
	            displayALertForm('加入失败,请重试');
	          }
	        });
      	}

		/*********************************DOM操作**********************************/		

		/*******************************全局变量区域*******************************/
		
		var isSlided=false;//侧边栏是否被滑出
		var footerIsDisplayed=false;//底部是否被显示

		//使用localSorage存储当前页面
		var pages=new StorageStack('index.php','index.php');
		localStorage.pageStack=pages;

		var postType=["一手好菜","玩转厨房","首页文章"];
		var replyType=["一手好菜","玩转厨房","首页文章"];
		var replyStatus=["正常","后台回复未读","用户已读"];

		var defaultPage=1;
		var defaultLimit=10;

		var isIndex=false;

		var WECHAT_APPID;
		var WECHAT_REDIRECT_URI;
		var WECHAT_SCOPE='snsapi_login';
		var WECHAT_STATE=Math.ceil(Math.random()*100);
		
		/*******************************全局变量区域*******************************/

	</script>
</head>

<body>
<?php include('login_column.php'); ?>


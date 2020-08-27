<?php
/*
Plugin Name: WP Add Search Keywords
Plugin URI: https://github.com/1000git/wp-add-search-keywords
Description: 人気のキーワードなどの検索キーワードリストを管理画面から登録し、表示させる。
Version: 1.1.0
Author: Chiba Takeshi
License: GPLv3 or later
*/


/* 管理画面にオリジナルメニューを追加する */
add_action( 'admin_menu', 'register_my_custom_menu_page__ask' );
function register_my_custom_menu_page__ask(){
    add_menu_page( '検索キーワードリスト設定', '検索キーワードリスト',
    'manage_options', 'add_search_keywords', 'mt_search_keywords', '', 6 );
}

// mt_settings_page() は Test Settings サブメニューのページコンテンツを表示
function mt_search_keywords() {

    // ユーザーが必要な権限を持つか確認する必要がある
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // フィールドとオプション名の変数
    $opt_name = 'mt_popular_keywords';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'mt_popular_keywords';

    // データベースから既存のオプション値を取得
    $opt_val = get_option( $opt_name );

    // ユーザーが何か情報を POST したかどうかを確認
    // POST していれば、隠しフィールドに 'Y' が設定されている
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // POST されたデータを取得
        $opt_val = $_POST[ $data_field_name ];

        // POST された値をデータベースに保存
        update_option( $opt_name, $opt_val );

        // 画面に「設定は保存されました」メッセージを表示
        ?>
		<div class="updated"><p><strong><?php _e('保存しました。', 'add-search-keyword' ); ?></strong></p></div>
		<?php
	};
	?>


	<div class="wrap">
	<h2>検索キーワードリスト設定</h2>
	<p>検索窓の下に表示させる検索文字列の候補（キーワード）を入力してください。</p>
	<p class="note"><span>::</span>をドラッグで並び替えが可能です。</p>

	<?php // $opt_valをカンマ区切りで配列にする
		$str = $opt_val;
		$arr = explode(',', $str);
	?>
	<hr />

	<form name="form1" method="post" action="">
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

		<div class="data-item">
			<ul id="sortableList">
			<?php for($i=0; $i<count($arr); $i++):
				echo '<li class="data-item__item-list"><span class="handle"></span><input type="text" name="item[]" value="'.$arr[$i].'" size="20" class="data-item__item"><span class="del-data-item"><span class="del-data-item__button"></span></span></li>';
			endfor; ?>
			</ul>
		</div>

		
		<script src="<?php echo plugins_url(); ?>/add-search-keywords/js/sortable.min.js"></script>
		<script>
			$(function(){

				new Sortable(sortableList, {
					animation: 150,
					ghostClass: 'blue-background-class',
					onUnchoose: function(evt) {
						adapt()
					},
					handle: ".handle"
				});
				// 初期表示
				if( $('.data-item__item-list').length===0) {
					//$('.data-item ul').append('<li class="data-item__no-item">検索キーワードがありません。</li>');
				}

				// 項目追加処理
				$('.add-data-item__button').on( 'click', function(){
					addItem()
					$('.data-item__no-item').remove()
				})
				// 入力時のデータ変更処理
				$(document).on( 'change','.data-item__item', function(){
					adapt()
				})
				//項目削除処理
				$(document).on( 'click', '.del-data-item__button', function(){
					$(this).parents('li').remove()
					adapt()
				})

				function adapt(){
					var dataArr = '';
					$('.data-item__item-list').each( function(){
						var itemData = $(this).find('.data-item__item').val()
						if(dataArr==''){
							if(itemData!=''){
								dataArr = itemData
							}
						} else {
							if(itemData!=''){
								dataArr = dataArr+','+itemData
							}
						}
					})
					$('.submit-arr').val(dataArr)
				}
				function addItem(){
					$('.data-item ul').append('<li class="data-item__item-list"><span class="handle"></span><input type="text" name="item[]" value="" size="20" class="data-item__item"><span class="del-data-item"><span class="del-data-item__button"></span></span></li>')
				}
			})
		</script>

		<div class="add-data-item"><button type="button" class="add-data-item__button">＋ 項目追加</button></div>
		<input type="hidden" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" class="submit-arr">

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary js-submit" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>

	</form>

<div class="add_search_keywords_publish">
	<h3>出力方法について</h3>
	<p>① テーマファイルの任意の箇所に以下を記述してください。</p>
	<pre>
&lt;?php add_search_keywords(); ?&gt;
</pre>

	<p>② 以下のようにHTMLが出力されます。<br>
	<pre>
&lt;ul class="search-keywords"&gt;
  &lt;li class="search-keywords__list"&gt;
    &lt;a class="search-keywords__list__link" href="{検索文字列}"&gt;
      &lt;span class="search-keywords__list__text"&gt;{homeURL}/?s={検索文字列}&lt;span&gt;
    &lt;/a&gt;
  &lt;/li&gt;
  &lt;li class="search-keywords__list"&gt;
    &lt;a class="search-keywords__list__link" href="{検索文字列}"&gt;
      &lt;span class="search-keywords__list__text"&gt;{homeURL}/?s={検索文字列}&lt;span&gt;
    &lt;/a&gt;
  &lt;/li&gt;
  ・・・
&lt;/ul&gt;
</pre>
</div>

	</div>
	<style>
		.data-item { }
		.data-item__item { border:solid 1px #ccc; border-radius: 3px; width:200px; line-height: 2em; height: 30px; font-size: 14px;}
		.data-item__item-list { border: dashed 1px transparent; background: #FFF; padding: 2px 5px 2px 2px; border-radius: 5px; display: inline-block; margin-right: 10px;}
		.add-data-item { width: 247px; display: inline-block;}
		.add-data-item__button { width: 247px; height: 38px; border: dashed 2px #aaa; border-radius: 3px; color: #aaa; display: block; width: 100%; line-height: 34px; background: beige; cursor: pointer;}
		.add-data-item__button:hover { background: antiquewhite; }
		.add-data-item__button:focus { outline: none; }

		.blue-background-class { opacity: 0.5; border: dashed 1px #ccc;}
		.blue-background-class .data-item__item { background: #ccc; }
		.handle { cursor: grab;}
		/* .handle:active {cursor: grabbing;} */
		.sortable-fallback:active { cursor: grabbing;}
		.handle:before { content: '::'; line-height: 30px; font-weight: bold; display: inline-block; width: 15px; text-align: center; vertical-align: top;}
		.handle:hover:before { color: #ccc;}
		.note { font-size: 12px; color: darkgoldenrod;}
		.note span { display: inline-block; background: #FFF; color: #000; border-radius:3px 0 0 3px; width: 14px; text-align: center; height:26px;line-height: 26px; vertical-align: middle; position: relative; top: -3px; margin-right: 5px;}
		.del-data-item { display: inline-block; vertical-align: top; position: relative; top: 9px;}
		.del-data-item__button { transform: rotate(-45deg); display: block; position: relative; margin-left: 7px; background: #ccc; width: 14px; height: 14px; border-radius: 50%; cursor: pointer;}
		.del-data-item__button:before,
		.del-data-item__button:after { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: block; background: #fff;  }
		.del-data-item__button:before {  width: 8px; height: 2px;}
		.del-data-item__button:after { width: 2px; height: 8px;}
		.del-data-item__button:hover { background: #000; }
		.add_search_keywords_publish { padding: 10px 20px; background: #fff; border-radius: 5px; }
		.add_search_keywords_publish h3 { color:#999; }
		.add_search_keywords_publish h3 span { color:#444; }
		.add_search_keywords_publish pre {  font-size: 12px; border-radius: 3px; box-shadow: inset 0 0 3px rgba(0,0,0,0.1); padding: 10px; background: #333; color: #ccc;}
	</style>
<?php
}

/* [出力方法]
 * <?php add_search_keywords(); ?>
 *
 * [出力結果]
 * <ul class="search-keywords">
 *  <li class="search-keywords__list">
 *		<a class="search-keywords__list__link" href="{検索文字列}">
 *			<span class="search-keywords__list__text">{homeURL}/?s={検索文字列}<span>
 *		</a>
 *	</li>
 *	<li class="search-keywords__list">
 *		<a class="search-keywords__list__link" href="{検索文字列}">
 *			<span class="search-keywords__list__text">{homeURL}/?s={検索文字列}<span>
 *		</a>
 *	</li>
 * 	…
 * </ul>
 */
function add_search_keywords(){
	$opt_val = get_option( 'mt_popular_keywords' );
	$arr = explode(',', $opt_val);
	if(count($arr)>1):
		echo '<ul class="search-keywords">'."\n";
		for($i=0; $i<count($arr); $i++):
			$str = urlencode($arr[$i]);
			echo '	<li class="search-keywords__list"><a href="'.home_url().'/?s='.$str.'" class="search-keywords__list__link"><span class="search-keywords__list__text">'.$arr[$i].'</span></a></li>'."\n";
		endfor;
		echo '</ul>'."\n";
	endif;
}

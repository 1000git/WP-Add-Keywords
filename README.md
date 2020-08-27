# WP Add Search Keywords
WordPressにて、検索窓の近くなどに任意の検索キーワードを直感的に追加・表示できるプラグイン。

## [使用方法]
有効化すると管理画面に検索キーワードリストというメニューが作成されます。
直感的に人気のキーワードなどの検索キーワードを追加していくことができます。
（マルチサイト非対応。現在のブログの検索結果へのリンクが生成されます。）


## [出力方法]
① テーマファイルの任意の箇所に以下を記述してください。

    <?php add_search_keywords(); ?>

② 以下のようにHTMLが出力されます。

    <ul class="search-keywords">
      <li class="search-keywords__list">
        <a class="search-keywords__list__link" href="{検索文字列}">
          <span class="search-keywords__list__text">{homeURL}/?s={検索文字列}<span>
        </a>
      </li>
      <li class="search-keywords__list">
        <a class="search-keywords__list__link" href="{検索文字列}">
          <span class="search-keywords__list__text">{homeURL}/?s={検索文字列}<span>
        </a>
      </li>
      ・・・
    </ul>



<nav>
  <ul class="pagination">
{%if $pagination->havePrev()%}
    <li>
      <a href="#" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
{%/if%}

{%for $p = $pagination->startPage() to $pagination->endPage()%}
    {%if $pagination->page()==$p%}
        <li class="active"><a href="{%build_url page=$p%}">{%$p+1%}</a></li>
    {%else%}
        <li><a href="{%build_url page=$p%}">{%$p+1%}</a></li>
    {%/if%}
{%/for%}

{%if $pagination->haveNext()%}
    <li>
      <a href="{%build_url page=$pagination->page()+1%}" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
{%/if%}
  </ul>
</nav>

/*

  (c) 2006 TUFaT.com. All Rights Reserved

*/

    var DEBUG = 0;

    var config = new Object ();

    config["smilesPATH"] = "{$smilesPATH}";
    config["smilesPerRow"] = {$smilesPerRow};

    config["smiles"] = new Object ();
    config["smiArray"] = new Array ();
{section name=i loop=$smiles}
    config["smiles"]["{$smiles[i].value}"] = "{$smiles[i].key}";    
{if $lastValue neq $smiles[i].key}
    config["smiArray"].push ("{$smiles[i].value}");
{/if}
{assign var="lastValue" value=$smiles[i].key}
{/section}

    config["themes"] = new Array ();

    config["getXMLURL"] = "{$connectTo}";

    config["roles"] = new Object ();

    config["roles"]["admin"] = "{$roles.admin}";
    config["roles"]["moderator"] = "{$roles.moderator}";

    /* sets the default english language */
    defaultLang = new Object ();
    defaultDialogLang = new Object ();

{section name=i loop=$lang}
    defaultLang["{$lang[i].type}"] = new Object ();
{foreach item=value from=$lang[i].content}
    defaultLang["{$lang[i].type}"]["{$value.name}"] = "{$value.value}";
{/foreach}
{/section}

{section name=i loop=$dlang}
    defaultDialogLang["{$dlang[i].type}"] = new Object ();
{foreach item=value from=$dlang[i].content}
    defaultDialogLang["{$dlang[i].type}"]["{$value.name}"] = "{$value.value}";
{/foreach}
{/section}

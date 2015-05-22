function toggleArrows()
{
  $(".arrows").toggle();
  if ($('.arrows').is(':visible'))
    $("#arrowToggle").html("Göm storlekspilar");
  else
    $("#arrowToggle").html("Visa storlekspilar");

  $.ajax({ url:     "/e/<?= $ID ?>/togglearrows", 
           type:    "POST",
           data:    {toggle: 1},
           success: function() { },
           error:   function() { }
    });
}

function toggleEdit()
{
  $(".edit").toggle();
  if ($('.edit').is(':visible'))
    $("#editToggle").html("Gå ut ur editeringsläge");
  else
    $("#editToggle").html("Gå in i editeringsläge");

  $.ajax({ url:     "/e/<?= $ID ?>/toggleedit", 
           type:    "POST",
           data:    {toggle: 1},
           success: function() { },
           error:   function() { }
    });

}

function updateTab(id)
{
  var data = {title: $("#dialog_title_"+id).val(),
              text: $("#dialog_text_"+id).val()};

  $("#tab_link-"+id).html(data.title);
  $("#tabs-"+id+" div.text").html(data.text);

  $.ajax({ url:     "/e/<?= $ID ?>/updatetab/"+id, 
           type:    "POST",
           data:    data,
           success: function() { $("#dialog_"+id).dialog("close"); },
           error:   function() { alert("Kommunikationen med servern misslyckades. Försök igen eller spara all text lokalt."); }
    });
}

function updateProjectDetails()
{
  var data = {title: $("#project_title").val()};

  $.ajax({ url:     "/e/<?= $ID ?>/updatemetadata", 
           type:    "POST",
           data:    data,
           success: function() { },
           error:   function() { alert("Kommunikationen med servern misslyckades. Försök igen eller spara all text lokalt."); }
    });
}

function updateCodeTab(tab, code)
{
  var data = {title: $("#code_title_"+tab+"-"+code).val()};
  $("#tab_code_link-"+tab+"-"+code).html(data.title);

  $.ajax({ url:     "/e/<?= $ID ?>/updatecodetitle/"+tab+"/"+code, 
           type:    "POST",
           data:    data,
           success: function() { $("#codedialog_"+tab+"-"+code).dialog("close"); },
           error:   function() { alert("Kommunikationen med servern misslyckades. Försök igen eller spara all text lokalt."); }
    });
}

function saveCode(editor, tab, code)
{
  var curr = editors[editor];

  var data = {code: curr.getValue()};

  $.ajax({ url:     "/e/<?= $ID ?>/updatecode/"+tab+"/"+code, 
           type:    "POST",
           data:    data,
           success: function() 
                    {
                      if (hasChanged['tabs-'+tab+'_code-'+code+'_editor'])
                      {
                        var oldHTML = $('#tab_code_link-'+tab+'-'+code).html();
                        $('#tab_code_link-'+tab+'-'+code).html(oldHTML.slice(0, - 1));
                      }
                      hasChanged['tabs-'+tab+'_code-'+code+'_editor'] = false;
                      $('#tab_code_link-'+tab+'-'+code).css("font-weight", "normal");
                      $('#tab_code_link-'+tab+'-'+code).css("text-decoration", "none");
                      $('#tab_link-'+tab).css("font-weight", "normal");
                      $('#tab_link-'+tab).css("text-decoration", "none");
                    },
           error:   function() { alert("Kommunikationen med servern misslyckades. Försök igen eller spara all text lokalt."); }
    });
  
}

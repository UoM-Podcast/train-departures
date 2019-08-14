var pixelIncrement = 2;
var scrollMillisecond = 50;
var refreshScrollWait = 3000;
var scrolled;

function Scroller()
{
    var scrollers = new Array();
    scrolled = new Array();
    var scroll;
    var qualifier = 'scroll';
    var counter = 0;
                
    scroll = document.getElementById('scroll0');
    
    while (scroll != null)
    {
        scroll.scrollTop = 0;
        scrollers[counter] = qualifier + counter;
        scrolled[counter] = false;
        counter++;
        scroll = document.getElementById(qualifier + counter);        
    }   
    scroll =  document.getElementById('NRESpace');
    
    if (scroll != null)
    {
        scroll.scrollTop = 0;
        scrollers[counter] = 'NRESpace';
        scrolled[counter] = false;
    }
        
    if (scrollers.length > 0)
    {        
        setTimeout(function(){Scroll(scrollers)},refreshScrollWait);
    }  
}
    
function Scroll(scrollers)
{    
    var pause = false;
    var loop = true;
    var scroll;
    
    for (i=0; i < scrollers.length; i++)
    {
        scroll = document.getElementById(scrollers[i]);
        
        if (scroll != null)
        {            
            if (scroll.scrollHeight > (scroll.scrollTop + scroll.offsetHeight))
            {
                scroll.scrollTop += pixelIncrement;
                
                if (scroll.scrollTop % scroll.offsetHeight == 0)
                {
			        pause = true;
                }
            }            
            if (scroll.scrollTop == 0 || (scroll.scrollTop >= (scroll.scrollHeight - scroll.offsetHeight)))
            {                
                scrolled[i] = true;
            }            
        }
    }
      
    for (i=0; i < scrolled.length; i++)
    {
        if (!scrolled[i])
        {
            loop = false;        
        }
    }
    
    if (!loop)
    {   
        if (pause)
        {
            setTimeout(function(){Scroll(scrollers)},refreshScrollWait);    
        }
        else
        {
            setTimeout(function(){Scroll(scrollers)},scrollMillisecond);    
        } 
    }
    else
    {        
        setTimeout(function(){Scroller()},refreshScrollWait);
    }
}
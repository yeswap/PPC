  document.getElementById("wrap").addEventListener("scroll",function(){
   var translate = "translate(0,"+this.scrollTop+"px)";
   this.querySelector("thead").style.transform = translate;
  });

    var eTbl = {"monCost": 0,"oper":1, "netw":2,"cost":3,"costTyp":4,"mins":5,"txts":6,"data":7,"throtld":8,"ID":9,"isPayGo":10,"HasRollover":11,"MMS":12,"plan":13}; //JavaScript "enum" for array row indexes
    
    var ePersist = {monCost:0,plan:1,cost:2,mins:3,txts:4,data:5,lineFee:6, multiLine:7, notes:8, oprID:9, AllowsHotspot:10, Hotspot_HS_Limit:11, Hotspot_HS_Throttle:12, HotspotThrottle:13, TextRoaming:14, VoiceRoaming:15, DataRoaming:16, AutopayDiscount:17, showWork:18};
    
    var eFmlyPln = {Cost:0, MinsShared:1, DataShared:2};
    
    //From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/indexOf (adds indexOf to old browsers like IE < 10)
    if (!Array.prototype.indexOf)
    {
      Array.prototype.indexOf = function(elt /*, from*/)
      {
        var len = this.length >>> 0;
    
        var from = Number(arguments[1]) || 0;
        from = (from < 0)
             ? Math.ceil(from)
             : Math.floor(from);
        if (from < 0)
          from += len;
    
        for (; from < len; from++)
        {
          if (from in this &&
              this[from] === elt)
            return from;
        }
        return -1;
      };
    }


    var loader = document.getElementById('loader');
    function showMsg() {
      loader.style.display = "block";
    }

    function findPlans() {
      var minElem = document.getElementById("mins");
      var mins = parseInt(minElem.value,10);
      if (isNaN(mins)) {mins = 0;}
      var textElem = document.getElementById("texts");
      var texts = parseInt(textElem.value,10);
      if (isNaN(texts)) {texts = 0;}
      var mbElem = document.getElementById("MB");
      var mb = parseInt(mbElem.value,10);
      if (isNaN(mb)) {mb = 0;}
      var mbgb = document.getElementById("MBGB").value;
      if (mbgb == "GB") {
        mb *= 1024;
      }
      var linesElem = document.getElementById("lines");
      var lines = parseInt(linesElem.value,10);
      var payGoCost = 0;
      var aryNetworks = [];
      var savedVals;
      
      if (document.getElementById("ATTyes").checked){
        aryNetworks.push("ATT");
      }
      if (document.getElementById("VZWyes").checked){
        aryNetworks.push("VZW");
      }
      if (document.getElementById("TMOyes").checked){
        aryNetworks.push("TMO");
      }
      if (document.getElementById("SPRyes").checked){
        aryNetworks.push("SPR");
      }
      var needsRoaming = document.getElementById("roaming").checked;
      var needsHotspot = document.getElementById("hotspot").checked;
      var needsIosMMS = document.getElementById("iosMMS").checked;
      var needsunlimTrot = document.getElementById("unlimTrot").checked;
      
      //alert("Mins=" + mins + "Texts=" + texts + "MB =" + mb + "Lines=" + lines);

      var table = document.getElementsByTagName('TABLE')[0];
      for (var i = 2; i < table.rows.length; i++) {
        row = table.rows[i];
        // restore modied rows
        reset(row.cells);
        //Check if user has excluded this row's network
        colNetwork = row.cells[eTbl.netw];
        rowNetwork = colNetwork.textContent;
        if (!networkOK(aryNetworks, rowNetwork)){
          row.style.display = "none";
          continue; // jump over this row
        }
        //If iOS MMS is required, hide rows that don't have it.
        if (needsIosMMS){
          if (row.cells[eTbl.MMS].innerHTML === "0"){
            row.style.display = "none";
            continue; // jump over this row
          }
        }
        //If unlimited throttled is required, hide rows that don't have it.
        if (needsunlimTrot){
          if (row.cells[eTbl.throtld].textContent.indexOf('kbps') == -1){
            row.style.display = "none";
            continue; // jump over this row
          }
        }
        colPlanID = row.cells[eTbl.ID];
        rowPlanID = parseInt(colPlanID.textContent,10);
        savedVals = Persist[rowPlanID][0];

        if (needsRoaming && !savedVals[ePersist.VoiceRoaming]){
          row.style.display = "none";
          continue;
        }
        if (needsHotspot && !savedVals[ePersist.AllowsHotspot]){
          row.style.display = "none";
          continue;
        }

        planLines = savedVals[ePersist.multiLine];
        thisMB = mb;
        if (planLines > 1 && lines != planLines){
          row.style.display = "none";
          continue; // jump over this row
        }
        
        if (planLines > 1) {
          thisMB *= planLines;
        }
        colMins = row.cells[eTbl.mins];
        if (colMins.textContent === "None") {
          rowMins = 0;
        }else{
            rowMins = parseFloat(colMins.textContent.replace(/[A-Za-z$-]/g, ""), 0); //Remove alpha and $ from "$0.10/ea" etc.
            if (colMins.textContent.indexOf('¢') >=0){
              rowMins = rowMins / 100;
            }
        }
        colTexts = row.cells[eTbl.txts];
        if (colTexts.textContent === "None") {
          rowTexts = 0;
        }else{
          rowTexts = parseFloat(colTexts.textContent.replace(/[A-Za-z$-]/g, ""), 0);
            if (colTexts.textContent.indexOf('¢') >=0){
              rowTexts = rowTexts / 100;
            }
        }
        colMB = row.cells[eTbl.data];
        
        if (colMB.textContent === "None") {
          rowMB = 0;
        }else{
          rowMB = parseFloat(colMB.textContent.replace(/[A-Za-z$-]/g, ""), 0);
        }
        if (colMB.textContent.indexOf('¢') >=0){
          rowMB = rowMB / 100;
        }
        if (colMB.textContent.indexOf('GB') >=0){
          rowMB = rowMB * 1024;
        }
        payGoCost = 0;
        if(rowMins < 1){
          payGoCost += mins * rowMins;
        }
        if(rowTexts < 1){payGoCost += texts * rowTexts;}
        if(rowMB < 1 || rowMB == 2.05){
          //use unrounded cost per MB from persist array, not rounded amount in table
          iVal = parseFloat(savedVals[ePersist.data]);
          payGoCost += thisMB * iVal;
        }
        colCost = row.cells[eTbl.cost];
        Cost = parseFloat(colCost.textContent);
        colCostPeriod = row.cells[eTbl.costTyp];
        CostPeriod = colCostPeriod.textContent;
        if (CostPeriod === '90 days') {
          costPerMo = Cost/3;
        }else if (CostPeriod === '60 days') {
          costPerMo = Cost/2;
        }else if (CostPeriod === '120 days') {
          costPerMo = Cost/4;
        }else if (CostPeriod === '180 days') {
          costPerMo = Cost/6;
        }else if (CostPeriod === 'year') {
          costPerMo = Cost/12;
        }else if (CostPeriod === 'day') {
          costPerMo = Cost * 30;
        }else if (CostPeriod === "doesn't expire") {
          costPerMo = 0;
        }else{
          costPerMo = Cost;
        }
        colPlan = row.cells[eTbl.plan];
        colPerMo = row.cells[eTbl.monCost];
        colIsPayGo = row.cells[eTbl.isPayGo];
        isPayGo = colIsPayGo.textContent;
        if (payGoCost && (isPayGo === "1")) {
          if (payGoCost > costPerMo) {
            costPerMo = payGoCost;
            colPerMo.textContent = payGoCost.toFixed(2);
          }else{
            colPerMo.textContent = costPerMo.toFixed(2);
          }
        }
        if ((colMB.textContent == "Unlimited" || rowMB >= thisMB) ||
        (rowMB > 0 && rowMB < 1) || (rowMB== 2.05)){
          dataOK = true;
        }else{
          dataOK = (tryAddon("d", row.cells, rowMB, thisMB));
        }
        if ((colMins.textContent == "Unlimited" || rowMins >= mins) ||
        (rowMins > 0 && rowMins < 1)){
            minsOK = true;
        }else{
          minsOK = (tryAddon("m", row.cells, rowMins, mins));
        }
        if((colTexts.textContent == "Unlimited" || rowTexts >= texts) ||
        (rowTexts > 0 && rowTexts < 1)){
          txtsOK = true;
        }else{
          txtsOK = tryAddon("t", row.cells, rowTexts, texts);
        }
        if(dataOK && minsOK && txtsOK) {
          costPerMo = parseFloat(row.cells[eTbl.monCost].textContent);
          nLineFee = parseFloat(savedVals[ePersist.lineFee]);
          costPerMo += nLineFee;
          row.style.display = "table-row";
          if(rowMB < 3 && isPayGo === "0"){
            // ProjectFi and other non PayGo with per MB data
            calcedCost = payGoCost + costPerMo;
            colPerMo.textContent = calcedCost.toFixed(2);
          }else{
            colPerMo.textContent = costPerMo.toFixed(2);
          }
        }else{
            row.style.display = "none";
            continue; // jump over this row
        }
        //If unlimited throttled is required, hide rows that
        //still don't have any data after addons.
        if (needsunlimTrot){
          if (row.cells[eTbl.data].textContent == "None"){
            row.style.display = "none";
            continue; // jump over this row
          }
        }

        if (lines > 1 && planLines == 0){
          CalcFmylPlans (lines, payGoCost, mb, mins);
        }
       } // end for
    sortTable("planTbl");
    scrollToTop(table);
    loader.style.display = "none";
  }
  function CalcFmylPlans (lines, payGoCost, mb, mins){
    var calcedCost = parseFloat(colPerMo.textContent);
    var FmlyPlnCst, i, bigPart, secondPart, testCost, linesFound;
    //Are there any family plans?
    if (FmlyPlns[rowPlanID] !== undefined){
      // Is there the exact plan we need?
      if (FmlyPlns[rowPlanID][lines] !== undefined){
        linesFound = lines;
        FmlyPlnCst = Number(FmlyPlns[rowPlanID][lines][eFmlyPln.Cost]);
      }else{
        //try with largest available family plans
        for (i = lines-1; i> 1; i--){
          if (FmlyPlns[rowPlanID][i] !== undefined){
            linesFound = i;
            FmlyPlnCst = Number(FmlyPlns[rowPlanID][i][eFmlyPln.Cost]);
            bigPart = linesFound;
            secondPart = lines - bigPart;
            testCost = Number(FmlyPlns[rowPlanID][bigPart][eFmlyPln.Cost]);
            if(FmlyPlns[rowPlanID][secondPart] !== undefined){
              FmlyPlnCst += Number(FmlyPlns[rowPlanID][secondPart][eFmlyPln.Cost]);
            }else{
              FmlyPlnCst += calcedCost * secondPart;
            }
          }
          break;
        }
        // try again spliting lines in half
        bigPart = Math.ceil(lines/2);
        secondPart = Math.floor(lines/2);
        if((FmlyPlns[rowPlanID][bigPart] !== undefined) &&
        (FmlyPlns[rowPlanID][secondPart] !== undefined)) {
          splitCost = Number(FmlyPlns[rowPlanID][secondPart][eFmlyPln.Cost]) +
          Number(FmlyPlns[rowPlanID][bigPart][eFmlyPln.Cost]);
          if (splitCost < FmlyPlnCst){
            FmlyPlnCst = splitCost;
          }
        }
      }
      savedVals = Persist[rowPlanID][0];
      if (payGoCost){
        FmlyPlnCst += payGoCost * lines;
      }else if (calcedCost > savedVals[ePersist.monCost]){
        FmlyPlnCst += calcedCost;
      }else{
        if (FmlyPlns[rowPlanID][linesFound][eFmlyPln.MinsShared] == "1" && mins * lines > rowMins){
          row.style.display = "none";
          return;
        }
        if (FmlyPlns[rowPlanID][linesFound][eFmlyPln.DataShared] =="1" && mb * lines > rowMB){
          row.style.display = "none";
          return;
        }
      }
    }else{
      // no family plans use single line cost * lines
      FmlyPlnCst = calcedCost * lines;
    }
    colPerMo.textContent = FmlyPlnCst.toFixed(2);
  }

function tryAddon(type, cells, have, need){
    var ary, $addonType;
  if(type == "d") {
      ary = DataAddons;
      addonType = "Data";
      amtKey = eTbl.data;
    }else if(type == "m"){
      ary = MinsAddons;
      addonType = "Mins";
      amtKey = eTbl.mins;
    }else if (type == "t") {
      ary = TxtsAddons;
      addonType = "Txts";
      amtKey = eTbl.txts;
    }
    //check for addon
    var eAddon = {Cost:0,Validity:1,Amt:2};
    var keepLooking = true;
    var rowFound = false; //Addons found?
    var isOK = false; //Found addon meets need?
    var k=1, totAddOnCost = 0;
    var addOns = 0;
    var sID = cells[eTbl.ID].textContent;
    var sKey = sID +k;
    var AddOnValidity, AddonAmt, AddonCost, dataNeeded, numMonths, costPerMo;
    while(ary[sKey] !== undefined && !isOK){
      AddOnValidity = ary[sKey][eAddon.Validity];
      if (AddOnValidity == "D" || AddOnValidity == "W"){
          return isOK;
      }
      rowFound = true;
      AddonAmt = parseFloat(ary[sKey][eAddon.Amt]);
      AddonCost = parseFloat(ary[sKey][eAddon.Cost]);
      costPerMo = parseFloat(cells[eTbl.monCost].textContent);
      if(AddonAmt + have >= need) {
        if (AddOnValidity == "0") {
          dataNeeded = need - have;
          have = need;
          numMonths = AddonAmt / dataNeeded;
          AddonCost = AddonCost / numMonths;
        } else {
          have += AddonAmt;
        }
        costPerMo += AddonCost;
        Persist[sID][0][ePersist.showWork] += "+$"+AddonCost.toFixed(2)+"^"+type;
        isOK= true;
      }
      k +=1;
      sKey = sID +k;
    }
    if (!rowFound){
      return isOK;
    }
    while(!isOK){
      if (AddonAmt == 0){
        //prevent infinte loop;
        console.log('illegal Addon Amount = zero. PlanID='+sID+', AddonCost='+AddonCost);
        return true;
      }
      have += AddonAmt;
      costPerMo += AddonCost;
      totAddOnCost += AddonCost;
      if (have >= need){
        isOK = true;
      }
    }
    if (isOK) {
      cells[eTbl.monCost].textContent = costPerMo.toFixed(2);
      if (type == "d"){
        if(have >= 1024){
          have = have/1024;
          cells[amtKey].textContent = have.toFixed() + " GB";
        }else{
          cells[amtKey].textContent = have.toFixed() + " MB";
        }
      }else{
        cells[amtKey].textContent = have.toFixed();
      }
      cells[eTbl.plan].textContent += " + " + addonType + " Addon";
      Persist[sID][0][ePersist.showWork] += "+$"+totAddOnCost.toFixed(2)+"^"+type;
    }
    return isOK;
  }
  
  function getFmlyPlns(sID){
    var retVal ="", sBefore = "<br/>";
    var dataShared, minsShared;
    var aryObj = FmlyPlns[sID];
    for(var lines in aryObj) {
      retVal += sBefore + lines + " Lines $" + aryObj[lines][eFmlyPln.Cost];
      dataShared = (aryObj[lines][eFmlyPln.DataShared] == '1');
      minsShared = (aryObj[lines][eFmlyPln.MinsShared] == '1');
      if(dataShared && minsShared){
        retVal += ", minutes and data shared";
      }else if (dataShared){
        retVal += ", data shared";
      }else if (minsShared){
        retVal += ", minutes shared";
      }
    }
    return retVal;
  }
  
  function getAddonsList(type, sID){
    var ary, addonType, sRetval;
  if(type == "d") {
      ary = DataAddons;
      addonType = "MB";
      sRetval = "<div><b>Data Addons:</b></div>";
    }else if(type == "m"){
      ary = MinsAddons;
      addonType = "Minutes";
      sRetval = "<div><b>Minutes Addons:</b></div>";
    }else if (type == "t") {
      ary = TxtsAddons;
      addonType = "Messages";
      sRetval = "<div><b>Messaging Addons:</b></div>";
    }
    //check for addon
    var eAddon = {Cost:0,Validity:1,Amt:2};
    var keepLooking = true;
    var rowFound = false; //Addons found?
    var isOK = false; //Found addon meets need?
    var k=1;
    var sKey = sID +k;
    var AddOnValidity, AddonAmt, AddonCost, sValidity;
    while(ary[sKey] !== undefined){
      AddOnValidity = ary[sKey][eAddon.Validity];
      switch (AddOnValidity) {
        case "1":
          sValidity = "expires with plan month";
          break;
        case "0":
          sValidity = "rolls over";
          break;
        case "3":
          sValidity = "good for 30 days";
          break;
        case "M":
          sValidity = "good for one month";
          break;
        case "9":
          sValidity = "good for 90 days";
          break;
        case "D":
          sValidity = "good for one day";
          break;
        case "W":
          sValidity = "good for one week";
          break;
        default:
          sValidity = "expires with plan month";
      }
      rowFound = true;
      AddonAmt = parseFloat(ary[sKey][eAddon.Amt]);
      AddonCost = parseFloat(ary[sKey][eAddon.Cost]);
      if(type == "d"){
        if (AddonAmt >= 1024){
          AddonAmt /= 1024;
          addonType = "GB";
        }
      }
      sRetval += "<div> $" + AddonCost + " for " +AddonAmt + " " + addonType + " " + sValidity + "</div>";
      k +=1;
      sKey = sID +k;
    }
    if (rowFound){
      return sRetval;
    }else{
      return "";
    }
  }
    
  function reset(cells){
    var id = cells[eTbl.ID].textContent;
    var savedVals = Persist[id][0];
    cells[eTbl.monCost].textContent = savedVals[ePersist.monCost];
    cells[eTbl.plan].textContent = savedVals[ePersist.plan];
    cells[eTbl.cost].textContent = savedVals[ePersist.cost].toFixed(2);
    cells[eTbl.mins].textContent = savedVals[ePersist.mins];
    cells[eTbl.txts].textContent = savedVals[ePersist.txts];
    iVal = parseFloat(savedVals[ePersist.data]);
    switch (true) {
      case iVal == -1:
        data = "Unlimited";
        break;
      case iVal === 0:
        data = "None";
        break;
      case iVal > 1023:
        data = iVal/1024;
        data = data.toFixed() + " GB";
        break;
      case iVal == 2.05:
        data = '$2.05/MB';
        break;
      case iVal >= 1:
        data = iVal + ' MB';
        break;
      case iVal > 1:
        data = '$'+iVal + '/MB';
        break;
      default:
        iVal = iVal*100;
        data = +(iVal).toFixed(2) +'¢/MB';
    }
    Persist[id][0][ePersist.showWork] = '';
    cells[eTbl.data].textContent = data;
  }
  function expandWork(showWork){
    showWork = showWork.replace('+', '+ ');
    showWork = showWork.replace('^d', ' Data Addon');
    showWork = showWork.replace('^m', ' Voice Addon');
    showWork = showWork.replace('^t', ' Messaging Addon');
    return showWork;
  }
  function scrollToTop(table){
    var row;
    for (var i = 0; i < table.rows.length; i++) {
      row = table.rows[i];
      if (row.style.display == "table-row"){
        row.scrollIntoView(false);
        break;
      }
    }
  }
  function networkOK(aryNetworks, rowNetwork){
    if(aryNetworks.indexOf(rowNetwork) > -1){
      return true;
    }else if ((rowNetwork.indexOf("Fi") > -1) &&
      ((aryNetworks.indexOf("SPR") > -1) ||
      aryNetworks.indexOf("TMO") > -1)) {
      return true;
    }else{
      return false;
    }
  }
    
// Fast sortTable funtion by Rob Wu shared on https://stackoverflow.com/questions/7558182/
function sortTable(tableId){
    var tbl = document.getElementById(tableId).tBodies[0];
    var store = [];
    for(var i=0, len=tbl.rows.length; i<len; i++){
        var row = tbl.rows[i];
        var sortnr = parseFloat(row.cells[0].textContent || row.cells[0].innerText);
        if(!isNaN(sortnr)) store.push([sortnr, row]);
    }
    store.sort(function(x,y){
        return x[0] - y[0];
    });
    for(var i=0, len=store.length; i<len; i++){
        tbl.appendChild(store[i][1]);
    }
    store = null;
}
    
// Get the modal
var modal = document.getElementById('myModal');
var moBrand = document.getElementById('moBrand');
var moPlan = document.getElementById('moPlan');
var moURL = document.getElementById('moURL');
var moNetw = document.getElementById('moNetw');
var moCost = document.getElementById('moCost');
var moCostType = document.getElementById('moCostTyp');
var moMonCost = document.getElementById('moMonCost');
var moTaxes = document.getElementById('moTaxes');
var moMins = document.getElementById('moMins');
var moTxts = document.getElementById('moTxts');
var moData = document.getElementById('moData');
var moThrotld = document.getElementById('moThrotld');
var moIsPayGo = document.getElementById('moIsPayGo');
var moHasRollover = document.getElementById('moHasRollover');
var moHotspot = document.getElementById('moHotspot');
var moNotes = document.getElementById('moNotes');
var moAddons = document.getElementById('moAddons');
var moCostLbl = document.getElementById('moCostLbl');
var moLineFee = document.getElementById('moLineFee');
var moRoaming = document.getElementById('moRoaming');
var moIosMms = document.getElementById('moIosMms');
var moProfile = document.getElementById('moProfile');
var moFmlyPlns = document.getElementById('moFmlyPlns');

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];


// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

//from https://stackoverflow.com/questions/5796718/
var decodeEntities = (function() {
  // this prevents any overhead from creating the object each time
  var element = document.createElement('div');

  function decodeHTMLEntities (str) {
    if(str && typeof str === 'string') {
      // strip script/html tags
      str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
      str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
      element.innerHTML = str;
      str = element.textContent;
      element.textContent = '';
    }

    return str;
  }

  return decodeHTMLEntities;
})();

function pageName(string){
  string = decodeEntities(string);
  string = string.toLowerCase();
  string = string.replace(/[^A-Z0-9]+/ig, "-")+".html";
  return string;
}
function addRowHandlers() {
  var table = document.getElementsByTagName('TABLE')[0];
  var rows = table.getElementsByTagName("tr");
  for (var i = 0; i < rows.length; i++) {
    var currentRow = table.rows[i];
    var createClickHandler =
      function(row) {
        return function() {
          var msg, sThrottled, sNetw, nTaxes, sTaxes;
          var fullURL, URLNoProtocol, sURL,retVal;
          var calcCost='';
          var cells = row.getElementsByTagName("td");
          var eOprMeta = {URL:0,Taxes:1,Notes:2,Suffix:3};
          var savedVals = Persist[cells[eTbl.ID].innerHTML][0];
          var OprMetaRow = OprMeta[savedVals[ePersist.oprID]];
          moBrand.innerHTML = cells[eTbl.oper].innerHTML;
          
          var sPlan = cells[eTbl.plan].innerHTML;
          var nPlusPos = sPlan.indexOf("+");
          if (nPlusPos > 0){
            sPlan = sPlan.replace("+", "Plan +");
          }else{
            sPlan += " Plan";
          }
          moPlan.innerText = decodeEntities(sPlan);
          fullURL= OprMetaRow[eOprMeta.URL];
          URLNoProtocol = fullURL.replace(/^https?\:\/\//i, "");
          sURL= "<a href='" + fullURL + "'>" + URLNoProtocol + "</a>";
          moURL.innerHTML = sURL;
          sNetw = cells[eTbl.netw].innerHTML;
          switch(sNetw) {
            case "ATT":
              moNetw.innerText = "AT&T";
              break;
            case "SPR":
              moNetw.innerText = "Sprint";
              break;
            case "VZW":
              moNetw.innerText = "Verizon";
              break;
            case "TMO":
              moNetw.innerText = "T-Mobile";
            break;
            case "Fi":
              moNetw.innerText = "Sprint + T-Mobile + US Cellular";
            break;
          }
          moCostLbl.innerText = parseInt(cells[eTbl.isPayGo].innerHTML) ? "Minimum Topup: " : "Base Plan Cost: ";
          moCost.innerText = cells[eTbl.cost].innerHTML;
          moCostType.innerText = cells[eTbl.costTyp].innerHTML;
          var nAutoPay = parseFloat(savedVals[ePersist.AutopayDiscount]);
          moAutopay.innerHTML = "";
          if (nAutoPay){
            moAutopay.innerHTML += "<b>Autopay Discount: </b>$" + nAutoPay.toFixed(2);
          }
          if (savedVals[ePersist.showWork]){
            calcCost= expandWork(savedVals[ePersist.showWork])+" + plan cost = ";
          }
          moMonCost.innerText = calcCost + cells[eTbl.monCost].innerHTML;
          nTaxes = parseInt(OprMetaRow[eOprMeta.Taxes]);
          if (nTaxes) {moMonCost.innerText += " + taxes";}
          nLineFee = parseFloat(savedVals[ePersist.lineFee]);
          if (nLineFee) {
            moCost.innerText += " + " + nLineFee + ' line fee';
          }
          
          switch (nTaxes){
            case 1: sTaxes = "Point of sale taxes"; break;
            case 2: sTaxes = "Point of sale and telecom taxes"; break;
            default: sTaxes = "None";
          }
          moTaxes.innerText = sTaxes;
          moMins.innerText = cells[eTbl.mins].innerHTML;
          moTxts.innerText = cells[eTbl.txts].innerHTML;
          moData.innerText = cells[eTbl.data].innerHTML;
          sThrottled = cells[eTbl.throtld].innerHTML;
          switch (sThrottled) {
            case "Hard capped":
              moThrotld.innerText = "No, hard capped";
              break;
            case "N/A":
              moThrotld.innerText = "N/A";
              break;
            default:
              moThrotld.innerText = "yes at " + sThrottled;
          }

          if (cells[eTbl.MMS].innerHTML === "0"){
            moIosMms.innerText = 'No';
          }else{
            moIosMms.innerText = 'Yes';
          }

          if(savedVals[ePersist.AllowsHotspot]){
            var Hotspot_HS_Limit = savedVals[ePersist.Hotspot_HS_Limit];
            if (Hotspot_HS_Limit){
              if (Hotspot_HS_Limit == -1){
                moHotspot.innerText = "unlimited";
              }else if (Hotspot_HS_Limit >= 1024){
                moHotspot.innerText = Hotspot_HS_Limit / 1024 + " GB ";
              } else{
                moHotspot.innerText = savedVals[ePersist.Hotspot_HS_Limit] + " MB ";
              }
            }else{
              moHotspot.innerText = cells[eTbl.data].innerHTML;
            }
            if(savedVals[ePersist.Hotspot_HS_Throttle]){
              if (savedVals[ePersist.Hotspot_HS_Throttle] >= 1024){
                moHotspot.innerText += " at " + savedVals[ePersist.Hotspot_HS_Throttle]/1024 + "mbps";
              }else{
                moHotspot.innerText += " at " + savedVals[ePersist.Hotspot_HS_Throttle] + "kbps";
              }
            }else if (moHotspot.innerText != "None"){
              moHotspot.innerText += " at high speeds";
            }
            if(savedVals[ePersist.HotspotThrottle]){
              if (savedVals[ePersist.HotspotThrottle >= 1024]){
                moHotspot.innerText += " then unlimited at " + savedVals[ePersist.HotspotThrottle]/1024 + "mbps";
              }else{
                moHotspot.innerText += " then unlimited at " + savedVals[ePersist.HotspotThrottle] + "kbps";
              }
            }
            if(savedVals[ePersist.AllowsHotspot] > 1){
              moHotspot.innerText += " for $" + savedVals[ePersist.AllowsHotspot] + "/mo";
            }
          }else{
            moHotspot.innerText = "not supported";
          }
          var nVoiceRoaming = savedVals[ePersist.VoiceRoaming];
          var nTextRoaming = savedVals[ePersist.TextRoaming];
          var nDataRoaming = savedVals[ePersist.DataRoaming];
          if(nVoiceRoaming || nTextRoaming || nDataRoaming){
            if (nVoiceRoaming == 0){
                moRoaming.innerText = "mins: none, ";
            }else if (nVoiceRoaming == -1){
              moRoaming.innerText = "mins: " + cells[eTbl.mins].innerHTML +', ';
            }else if (nVoiceRoaming >1){
              moRoaming.innerText = "mins: " + nVoiceRoaming +",  ";
            }else if (nVoiceRoaming <1){
              moRoaming.innerText = "mins: " + nVoiceRoaming.toFixed(2) +"¢/ea, ";
            }
            if (nTextRoaming == 0){
              moRoaming.innerText += "txts: none, ";
            }else if (nTextRoaming == -1){
              moRoaming.innerText += "txts: " + cells[eTbl.txts].innerHTML +', ';
            }else if (nTextRoaming >1){
              moRoaming.innerText += "txts: " + nTextRoaming +", ";
            }else if (nTextRoaming <1){
              moRoaming.innerText += "txts: " + nTextRoaming.toFixed(2) * 100 +"¢/ea, ";
            }
            if (nDataRoaming == 0){
               moRoaming.innerText += "data: none";
            }else if (nDataRoaming == -1){
              moRoaming.innerText += "data: " + cells[eTbl.data].innerHTML;
            }else if (nDataRoaming >1){
              if (nDataRoaming> 1024){
                moRoaming.innerText += "data: " + nDataRoaming/1024 + " GB";
              }else{
                moRoaming.innerText += "data: " + nDataRoaming +" MB";
              }
            }else if (nDataRoaming <1){
              moRoaming.innerText += "data: " + nDataRoaming.toFixed(2) *100 +"¢/MB";
            }
          }else{
            moRoaming.innerText = "none";
          }
          moIsPayGo.innerText = parseInt(cells[eTbl.isPayGo].innerHTML) ? "yes" : "no";
          moHasRollover.innerText = parseInt(cells[eTbl.HasRollover].innerHTML) ? "yes" : "no";
          if (FmlyPlns[cells[eTbl.ID].innerHTML] !== undefined) {
            moFmlyPlns.innerHTML = "<b>Family Plans:</b> "
            + getFmlyPlns(cells[eTbl.ID].innerHTML);
          }else{
            moFmlyPlns.innerHTML = "";
          }
          moNotes.innerHTML = savedVals[ePersist.notes];
          if(moNotes.innerHTML){moNotes.innerHTML += " ";}
          if (OprMetaRow[eOprMeta.Notes]){
            moNotes.innerHTML += OprMetaRow[eOprMeta.Notes];
          }
          if(moNotes.innerHTML){
            moNotes.innerHTML = "<b>Notes:</b> " + moNotes.innerHTML;
          }
          retVal = getAddonsList("m",cells[eTbl.ID].innerHTML );
          retVal += getAddonsList("t",cells[eTbl.ID].innerHTML );
          retVal += getAddonsList("d",cells[eTbl.ID].innerHTML );
          moAddons.innerHTML = retVal;
          var sOper = cells[eTbl.oper].innerHTML;
          var sOperURL = sOper;
          var Suffix = OprMetaRow[eOprMeta.Suffix];
          if (Suffix){
            sOperURL += "-" + Suffix;
          }
          sOperURL = pageName(sOperURL);
          
          moProfile.innerHTML = "<br/><b>For more information about " + sOper + " please visit our <a href='profiles/" + sOperURL + "'>" + sOper + " OperatorProfile</a>";
          modal.style.display = "block";
        };
      };
    currentRow.onclick = createClickHandler(currentRow);
  }
}
window.onload = addRowHandlers();

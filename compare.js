document.getElementById("wrap").addEventListener("scroll",function(){
   var translate = "translate(0,"+this.scrollTop+"px)";
   this.querySelector("thead").style.transform = translate;
  });
// globals
    var eTbl = {"monCost": 0,"oper":1, "netw":2,"mins":3,"txts":4,"data":5,"ID":6}; //JavaScript "enum" for array row indexes
    
    var ePersist = {monCost:0,mins:1,txts:2,data:3};
    
    var eRow = {plan:0,cost:1,costType:2,overageThrottle:3, isPayGo:4, HasRollover:5, LineFee:6, multiLine:7, notes:8, oprID:9, AllowsHotspot:10, Hotspot_HS_Limit:11, Hotspot_HS_Throttle:12, HotspotThrottle:13, TextRoaming:14, VoiceRoaming:15, DataRoaming:16, AutopayDiscount:17, MMS:18, showWork:19};
    
    var eFmlyPln = {Cost:0, MinsShared:1, TxtsShared:2, DataShared:3, AutopayDiscount:4};
    
    //temporary globals until plan? and request? objects are implemented
    var row, Persist, rowMins, rowMB, rowTexts, colPerMo;
    
    //From https://stackoverflow.com/questions/1144783/how-to-replace-all-occurrences-of-a-string-in-javascript
    String.prototype.replaceAll = function(search, replacement) {
      var target = this;
      return target.split(search).join(replacement);
    };
    
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
      // read web form
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
      var aryNetworks = [];
      
      var linesElem = document.getElementById("lines");
      var lines = parseInt(linesElem.value,10);
      var payGoCost, AutopayDiscount, bAppendMonCost, monCstDesc;
      var savedVals, addonUsed, tempCost, thisMB;
      var colNetwork, rowNetwork, colPlanID, rowPlanID, planLines;
      var colMins, colTexts, colMB, iVal, colCost, Cost, colCostPeriod;
      var colPlan, costPerMo, colIsPayGo, isPayGo, CostPeriod, dataOK;
      var minsOK, txtsOK, nLineFee, calcedCost, tmpCost, rowMeta;
      
      
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
      var autoPay = document.getElementById("Autopay").checked;

      // loop thru table calculating cost and hiding rows that don't meet needs.
    
      var table = document.getElementsByTagName('TABLE')[0];
      for (var i = 2; i < table.rows.length; i++) {
        payGoCost = 0, bAppendMonCost = false, monCstDesc = "";
        row = table.rows[i];
        // restore modied rows
        reset(row.cells);
        // get plan ID and init Persist array
        colPlanID = row.cells[eTbl.ID];
        rowPlanID = parseInt(colPlanID.textContent,10);
        //console.log (rowPlanID);
        savedVals = Persist[rowPlanID][0];
        rowMeta = RowAry[rowPlanID][0];

        //Check if user has excluded this row's network
        colNetwork = row.cells[eTbl.netw];
        rowNetwork = colNetwork.textContent;
        if (!networkOK(aryNetworks, rowNetwork)){
          row.style.display = "none";
          continue; // jump over this row
        }
        //If iOS MMS is required, hide rows that don't have it.
        if (needsIosMMS){
          if (rowMeta[eRow.MMS] === 0){
            row.style.display = "none";
            continue; // jump over this row
          }
        }
        //If unlimited throttled is required, hide rows that don't have it.
        colMB = row.cells[eTbl.data];
        if (needsunlimTrot && colMB.textContent != "Unlimited"){
          if (rowMeta[eRow.overageThrottle] < 32){
            row.style.display = "none";
            continue; // jump over this row
          }
        }

        if (needsRoaming && !rowMeta[eRow.VoiceRoaming]){
          row.style.display = "none";
          continue;
        }
        if (needsHotspot && !rowMeta[eRow.AllowsHotspot]){
          row.style.display = "none";
          continue;
        }

        planLines = rowMeta[eRow.multiLine];
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
        if(rowTexts < 1){
          payGoCost += texts * rowTexts;
        }
        if(rowMB < 1 || rowMB == 2.05){
          //use unrounded cost per MB from persist array, not rounded amount in table
          iVal = parseFloat(savedVals[ePersist.data]);
          payGoCost += thisMB * iVal;
        }
        Cost = parseFloat(rowMeta[eRow.cost]);
        AutopayDiscount = parseFloat(rowMeta[eRow.AutopayDiscount]);
        if(autoPay && AutopayDiscount > 0){
          tempCost = Cost - AutopayDiscount;
          monCstDesc = "$" + Cost + " - $" + AutopayDiscount + " autopay discount = $" + tempCost;
        }else{
          tempCost = Cost;
          monCstDesc = "$" + Cost;
        }

        CostPeriod = rowMeta[eRow.costType];
        if (CostPeriod === 9) {
          costPerMo = tempCost/3;
          monCstDesc += "/3 = $" + costPerMo.toFixed(2);
        }else if (CostPeriod === 6) {
          costPerMo = tempCost/2;
          monCstDesc += "/2 = $" + costPerMo.toFixed(2);
        }else if (CostPeriod === 4) {
          costPerMo = tempCost/4;
          monCstDesc += "/4 = $" + costPerMo.toFixed(2);
        }else if (CostPeriod === 'S') {
          costPerMo = tempCost/6;
          monCstDesc += "/6 = $" + costPerMo.toFixed(2);
        }else if (CostPeriod === 'Y') {
          costPerMo = tempCost/12;
          monCstDesc += "/12 = $" + costPerMo.toFixed(2);
        }else if (CostPeriod === 'D') {
          costPerMo = tempCost * 30;
          monCstDesc += " times 30 = $" + costPerMo.toFixed(2);
        }else if (CostPeriod === 0) {
          costPerMo = 0;
          monCstDesc = "$0";
        }else{
          costPerMo = tempCost;
        }
        nLineFee = parseFloat(rowMeta[eRow.LineFee]);
        colPlan = rowMeta[eRow.plan];
        colPerMo = row.cells[eTbl.monCost];
        isPayGo = rowMeta[eRow.isPayGo];
        if (payGoCost && (isPayGo === 1)) {
          if (payGoCost > costPerMo) {
            costPerMo = payGoCost;
            tmpCost = costPerMo + nLineFee;
            colPerMo.textContent = tmpCost.toFixed(2);
            monCstDesc = "$" + payGoCost.toFixed(2) + " PayGo cost";
          }else{
            costPerMo += nLineFee;
            colPerMo.textContent = costPerMo.toFixed(2);
            monCstDesc = "$" + colPerMo.textContent + " minimum monthly cost";
          }
        }else{
          //costPerMo -= AutopayDiscount;
          costPerMo += nLineFee;
          colPerMo.textContent = costPerMo.toFixed(2);
        }
        addonUsed = false;
        if ((colMB.textContent == "Unlimited" || rowMB >= thisMB) ||
        (rowMB > 0 && rowMB < 1) || (rowMB== 2.05)){
          dataOK = true;
        }else{
          dataOK = (tryAddon("d", row.cells, rowMB, thisMB));
          addonUsed = dataOK;
        }
        if ((colMins.textContent == "Unlimited" || rowMins >= mins) ||
        (rowMins > 0 && rowMins < 1)){
            minsOK = true;
        }else{
          minsOK = (tryAddon("m", row.cells, rowMins, mins));
          addonUsed = minsOK;
        }
        if((colTexts.textContent == "Unlimited" || rowTexts >= texts) ||
        (rowTexts > 0 && rowTexts < 1)){
          txtsOK = true;
        }else{
          txtsOK = tryAddon("t", row.cells, rowTexts, texts);
          addonUsed = txtsOK;
        }
        if(dataOK && minsOK && txtsOK) {
          costPerMo = parseFloat(row.cells[eTbl.monCost].textContent);
          row.style.display = "table-row";
          if(rowMB < 3 && isPayGo === "0" && payGoCost){
            // ProjectFi and other non PayGo with per MB data
            calcedCost = payGoCost + costPerMo;
            monCstDesc = costPerMo + " + " + payGoCost + "PayGo cost";
            colPerMo.textContent = calcedCost.toFixed(2);
          }else{
            colPerMo.textContent = costPerMo.toFixed(2);
          }
          if (nLineFee){
            monCstDesc += " + $" + nLineFee + " line fee";
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

        if (lines > 1 && planLines === 0){
          monCstDesc = CalcFmylPlans (row.cells, lines, payGoCost, mb, mins, texts, monCstDesc, autoPay);
          rowMeta[eRow.showWork] = monCstDesc;
        }else if (rowMeta[eRow.showWork]) {
          rowMeta[eRow.showWork] = monCstDesc + rowMeta[eRow.showWork];
        }else{
          rowMeta[eRow.showWork] = monCstDesc;
        }
        if (bAppendMonCost || (addonUsed && lines ==1)) {
          rowMeta[eRow.showWork] += " = " + colPerMo.textContent;
        }
       } // end for
    sortTable("planTbl");
    scrollToTop(table);
    if(window.innerWidth < 976){
      window.location.href = "#plans";
    }
    loader.style.display = "none";
  }
  function CalcFmylPlans (cells, lines, payGoCost, mb, mins, texts, monCstDesc, autoPay){
    var calcedCost = parseFloat(colPerMo.textContent), i, j, splitCost;
    var FmlyPlnCst, bigPart, secondPart, testCost, linesFound = 0;
    var rowPlanID = parseInt(cells[eTbl.ID].textContent,10), bigCost;
    var AutopayDiscount, smallCost, needsTotal = false;
    var savedVals = Persist[rowPlanID][0], tmpShowWork, tmpCost;
    var rowMeta = RowAry[rowPlanID][0], autoPayBigDiscount;
    var addonDesc = rowMeta[eRow.showWork], fmlyPlanDesc;
    var prependAddon = true, autoPaySmallDiscount, autoPay;

    //Are there any family plans?
    if (FmlyPlns[rowPlanID] !== undefined){
      prependAddon = false;
      // Is there the exact plan we need?
      if (FmlyPlns[rowPlanID][lines] !== undefined){
        linesFound = lines;
        FmlyPlnCst = Number(FmlyPlns[rowPlanID][lines][eFmlyPln.Cost]);
        AutopayDiscount = Number(FmlyPlns[rowPlanID][lines][eFmlyPln.AutopayDiscount]);
        if (autoPay && AutopayDiscount > 0){
          fmlyPlanDesc = "$" + FmlyPlnCst + " " + lines + " line plan" + " - $"  + AutopayDiscount + " auto pay discount = $" + (FmlyPlnCst - AutopayDiscount);
          FmlyPlnCst -= AutopayDiscount;
        }else{
          fmlyPlanDesc = "$" + FmlyPlnCst + " " + lines + " line plan";
        }
        //savedVals[ePersist.showWork contains addons
        if (rowMeta[eRow.showWork]){
          needsTotal = true;
        }
        monCstDesc = "";
      }else{
        //try with largest available family plans
        for (i = lines-1; i> 1; i--){
          if (FmlyPlns[rowPlanID][i] !== undefined){
            linesFound = i;
            bigCost = Number(FmlyPlns[rowPlanID][i][eFmlyPln.Cost]);
            FmlyPlnCst = bigCost;
            AutopayDiscount = Number(FmlyPlns[rowPlanID][i][eFmlyPln.AutopayDiscount]);
            bigPart = linesFound;
            secondPart = lines - bigPart;
            j = 1;
            while(secondPart > bigPart){
              secondPart -= bigPart;
              FmlyPlnCst += bigCost;
              j++;
            }
            if (autoPay && AutopayDiscount > 0){
              tmpShowWork = j + " $" + bigCost + " " + bigPart + " line plan" + (j==1 ? " " : "s ") + " - $"  + AutopayDiscount + " auto pay discount = $" + (bigCost - AutopayDiscount);
              bigCost -= AutopayDiscount
            }else{
              tmpShowWork = j + " $" + bigCost + " " + bigPart + " line plan" + (j==1 ? " " : "s ");
            }
            if(FmlyPlns[rowPlanID][secondPart] !== undefined){
              smallCost = Number(FmlyPlns[rowPlanID][secondPart][eFmlyPln.Cost]);
              FmlyPlnCst += smallCost;
              if (autoPay && AutopayDiscount > 0){
                tmpShowWork +=  "+ 1 $" + smallCost + " " + secondPart + " line  plan " + " - $"  + AutopayDiscount + " auto pay discount = $" + (smallCost - AutopayDiscount);
                smallCost -= AutopayDiscount
              }else{
                tmpShowWork +=  "+ 1 $" + smallCost + " " + secondPart + " line  plan ";
              }
             }else{
              // *** need to brak out family plan discount from calcedCost ***
              FmlyPlnCst += calcedCost * secondPart;
              if (secondPart == 1){
                tmpShowWork += "+ 1 " + monCstDesc + " 1 line plan";
              } else {
                tmpShowWork += "+ " + secondPart + " " + monCstDesc + " 1 line plans";
              }
            }
            needsTotal = true;
            break;
          }
        }
        // try again spliting lines in half
        bigPart = Math.ceil(lines/2);
        secondPart = Math.floor(lines/2);
        if((FmlyPlns[rowPlanID][bigPart] !== undefined) &&
        (FmlyPlns[rowPlanID][secondPart] !== undefined)) {
          bigCost = Number(FmlyPlns[rowPlanID][bigPart][eFmlyPln.Cost]);
          smallCost = Number(FmlyPlns[rowPlanID][secondPart][eFmlyPln.Cost]);
          autoPayBigDiscount = Number(FmlyPlns[rowPlanID][bigPart][eFmlyPln.AutopayDiscount]);
          autoPaySmallDiscount = Number(FmlyPlns[rowPlanID][secondPart][eFmlyPln.AutopayDiscount]);
          splitCost = bigCost + smallCost - autoPayBigDiscount - autoPaySmallDiscount;
          if (splitCost < FmlyPlnCst){
            FmlyPlnCst = splitCost;
            if (bigPart === secondPart){
              fmlyPlanDesc = "2 $" + bigCost + " " + bigPart + " line family plans";
              if (autoPay && autoPayBigDiscount){
                fmlyPlanDesc += " - $" + (autoPayBigDiscount * 2) + " auto pay discount";
              }
            }else{
              fmlyPlanDesc = "$" + bigCost + " " + bigPart + " line  plan";
              if(autoPay && autoPayBigDiscount){
                fmlyPlanDesc += " - $" + autoPayBigDiscount + " auto pay discount";
              }
              fmlyPlanDesc +=  " + $" + smallCost + " + " + secondPart + " line plan";
              if(autoPay && autoPaySmallDiscount){
                fmlyPlanDesc += " - $" + autoPaySmallDiscount+ " auto pay discount";
              }
            }
          }else{
            fmlyPlanDesc = tmpShowWork;
          }
        }else{
          fmlyPlanDesc = tmpShowWork;
        }
        monCstDesc = "";
      }
      if (payGoCost){
        FmlyPlnCst += payGoCost * lines;
      }
    }
    if(linesFound){
      //Family plan(s) found. Do we need any addons?
      if (FmlyPlns[rowPlanID][linesFound][eFmlyPln.MinsShared] == "1"){
        if( mins * lines > rowMins){
          rowMeta[eRow.showWork] = "";
          cells[eTbl.monCost].textContent = "0.00";
          if (tryAddon("m", cells, rowMins, mins * lines)){
            fmlyPlanDesc += rowMeta[eRow.showWork];
            FmlyPlnCst += parseFloat(cells[eTbl.monCost].textContent);
          }else{
            row.style.display = "none";
            return;
          }
        }
      }else if(mins > rowMins){
        // mins not shared
        rowMeta[eRow.showWork] = "";
        cells[eTbl.monCost].textContent = "0.00";
        if (tryAddon("m", cells, rowMins, mins, lines)){
          fmlyPlanDesc += rowMeta[eRow.showWork];
          FmlyPlnCst += parseFloat(cells[eTbl.monCost].textContent);
        }else{
          row.style.display = "none";
          return;
        }
      }
      if (FmlyPlns[rowPlanID][linesFound][eFmlyPln.TxtsShared] == "1") { if (texts * lines > rowTexts){
          rowMeta[eRow.showWork] = "";
          cells[eTbl.monCost].textContent = "0.00";
          if (tryAddon("t", cells, rowTexts, texts * lines)){
            fmlyPlanDesc += rowMeta[eRow.showWork];
            FmlyPlnCst += parseFloat(cells[eTbl.monCost].textContent);
          }else{
            row.style.display = "none";
            return;
          }
        }
      }else if(texts > rowTexts){
        rowMeta[eRow.showWork] = "";
        cells[eTbl.monCost].textContent = "0.00";
        if (tryAddon("t", cells, rowTexts, texts, lines)){
          fmlyPlanDesc += rowMeta[eRow.showWork];
          FmlyPlnCst += parseFloat(cells[eTbl.monCost].textContent);
        }else{
          row.style.display = "none";
          return;
        }
      }
      if (FmlyPlns[rowPlanID][linesFound][eFmlyPln.DataShared] =="1") {
        if(mb * lines > rowMB){
          rowMeta[eRow.showWork] = "";
          cells[eTbl.monCost].textContent = "0.00";
          if (tryAddon("d", cells, rowMB, mb * lines)){
            fmlyPlanDesc += rowMeta[eRow.showWork];
            FmlyPlnCst += parseFloat(cells[eTbl.monCost].textContent);
          }else{
            row.style.display = "none";
            return;
          }
        }
      }else if(mb > rowMB){
        // data not shared
        rowMeta[eRow.showWork] = "";
        cells[eTbl.monCost].textContent = "0.00";
        if (tryAddon("d", cells, rowMB, mb , lines)){
          fmlyPlanDesc += rowMeta[eRow.showWork];
          FmlyPlnCst += parseFloat(cells[eTbl.monCost].textContent);
        }else{
          row.style.display = "none";
          return;
        }
      }
    }else{
      // no family plans use single line cost * lines
      FmlyPlnCst = calcedCost * lines;
      fmlyPlanDesc = " times " + lines + " lines";
      needsTotal = true;
    }

    colPerMo.textContent = FmlyPlnCst.toFixed(2);
    if(prependAddon){
      monCstDesc += addonDesc + fmlyPlanDesc;
    }else{
      monCstDesc += fmlyPlanDesc;
    }
    if(needsTotal){
      monCstDesc += " = $" + colPerMo.textContent;
    }
    return monCstDesc;
  }

function tryAddon(type, cells, have, need, lines){
  lines = lines || 1; //assign 1 if undefinedF
  var ary, addonType, addonLabel, amtKey, sAddonCout;
  if(type == "d") {
      ary = DataAddons;
      addonType = "Data";
      addonLabel = 'data addon';
      amtKey = eTbl.data;
    }else if(type == "m"){
      ary = MinsAddons;
      addonType = "Mins";
      addonLabel = 'voice addon';
      amtKey = eTbl.mins;
    }else if (type == "t") {
      ary = TxtsAddons;
      addonType = "Txts";
      addonLabel = 'text addon';
      amtKey = eTbl.txts;
    }
    //check for addon
    var eAddon = {Cost:0,Validity:1,Amt:2};
    var keepLooking = true;
    var rowFound = false; //Addons found?
    var isOK = false; //Found addon meets need?
    var k=1;
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
      if(AddonAmt == -1){
        have = -1;
        isOK = true;
        costPerMo += (AddonCost * lines);
      }else{
        if(AddonAmt + have >= need) {
        if (AddOnValidity == "0") {
          // 0 == roll over
          dataNeeded = need - have;
          have = need;
          numMonths = AddonAmt / dataNeeded;
          AddonCost = AddonCost / numMonths;
        } else {
          have += AddonAmt;
        }
        costPerMo += (AddonCost * lines);
        //Persist[sID][0][ePersist.showWork] += "+$"+AddonCost.toFixed(2)+"^"+type;
        isOK= true;
        }
      }
      k +=1;
      sKey = sID +k;
    }
    if (!rowFound){
      return isOK;
    }
    var addonCount = 0;
    while(!isOK){
      if (AddonAmt === 0){
        //prevent infinte loop;
        console.log('illegal Addon Amount = zero. PlanID='+sID+', AddonCost='+AddonCost);
        return true;
      }
      have += AddonAmt;
      costPerMo += (AddonCost * lines);
      if (have >= need){
        isOK = true;
      }
      addonCount++;
    }
    if(addonCount === 0){
      addonCount = 1;
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
        if (have ==  -1){
          cells[amtKey].textContent = "Unlimited";
        }else{
          cells[amtKey].textContent = have.toFixed();
        }
      }
      Persist[sID][0][ePersist.plan] += " + " + addonType + " Addon";
      sAddonCout = "";
      if(lines > 1){
        addonCount *= lines;
      }

      if (addonCount > 2){
        sAddonCout = addonCount;
      }
        

      RowAry[sID][0][eRow.showWork] += " + " + sAddonCout + " $"+AddonCost.toFixed(2) + " " + addonLabel;
      if (addonCount > 1) {
        RowAry[sID][0][eRow.showWork] += "s";
      }
    }
    return isOK;
  }
  
  function getFmlyPlns(sID){
    var retVal ="", sBefore = "<br/>";
    var dataShared, minsShared;
    var aryObj = FmlyPlns[sID];
    for(var lines in aryObj) {
      retVal += sBefore + lines + " Lines $" + aryObj[lines][eFmlyPln.Cost];
      AutopayDiscount = aryObj[lines][eFmlyPln.AutopayDiscount];
      if(AutopayDiscount > 0){
         retVal += ", $" + AutopayDiscount + " auto pay discount";
      }
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
      addonType = "minutes";
      sRetval = "<div><b>Minutes Addons:</b></div>";
    }else if (type == "t") {
      ary = TxtsAddons;
      addonType = "messages";
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
      if(AddonAmt == -1){
        AddonAmt = 'unlimited';
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
    var id = cells[eTbl.ID].textContent, iVal, data;
    var savedVals = Persist[id][0];
    cells[eTbl.monCost].textContent = savedVals[ePersist.monCost];
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
    RowAry[id][0][eRow.showWork] = '';
    cells[eTbl.data].textContent = data;
  }
  /*
  function expandWork(showWork){
    showWork = showWork.replaceAll('+', ' + ');
    showWork = showWork.replaceAll('^d', ' data addon');
    showWork = showWork.replaceAll('^m', ' voice addon');
    showWork = showWork.replaceAll('^t', ' messaging addon');
    showWork = showWork.replaceAll('^x', ' per line times ');
    return showWork;
  }
  */
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
    var store = [], i, len;
    for(i=0, len=tbl.rows.length; i<len; i++){
        var row = tbl.rows[i];
        var sortnr = parseFloat(row.cells[0].textContent || row.cells[0].innerText);
        if(!isNaN(sortnr)) store.push([sortnr, row]);
    }
    store.sort(function(x,y){
        return x[0] - y[0];
    });
    for(i=0, len=store.length; i<len; i++){
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
var moAutopay = document.getElementById('moAutopay');

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
          var msg, nThrottled, sNetw, nTaxes, sTaxes;
          var fullURL, URLNoProtocol, sURL,retVal;
          var calcCost='', CostPeriod;
          var cells = row.getElementsByTagName("td");
          var eOprMeta = {URL:0,Taxes:1,Notes:2,Suffix:3};
          var rowPlanID = cells[eTbl.ID].innerHTML;
          var savedVals = Persist[rowPlanID][0];
          var rowMeta = RowAry[rowPlanID][0];
          var OprMetaRow = OprMeta[rowMeta[eRow.oprID]];
          moBrand.innerHTML = cells[eTbl.oper].innerHTML;
          var sPlan = rowMeta[eRow.plan];
          var nPlusPos = sPlan.indexOf("+");
          var lines = parseInt(document.getElementById("lines").value,10);

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
          moCostLbl.innerText = parseInt(rowMeta[eRow.isPayGo]) ? "Minimum Topup: " : "Base Plan Cost: ";
          moCost.innerText = '$' + rowMeta[eRow.cost];
          CostPeriod = rowMeta[eRow.costType];
          if (CostPeriod === 9) {
             moCostType.innerText = '90 days';
          }else if (CostPeriod === 6) {
            moCostType.innerText = '60 days';
          }else if (CostPeriod === 4) {
            moCostType.innerText = '120 days';
          }else if (CostPeriod === 3) {
            moCostType.innerText = '30 days';
          }else if (CostPeriod === 'S') {
            moCostType.innerText = '6 months';
          }else if (CostPeriod === 'Y') {
            moCostType.innerText = 'year';
          }else if (CostPeriod === 'D') {
            moCostType.innerText = 'day';
          }else if (CostPeriod === 'M') {
            moCostType.innerText = 'month';
          }else if (CostPeriod === 0) {
            moCostType.innerText = 'doesn\'t expire';
          }else{
            moCostType.innerText = CostPeriod;
          }
          var nAutoPay = parseFloat(rowMeta[eRow.AutopayDiscount]);
          moAutopay.innerHTML = "";
          if (nAutoPay){
            moAutopay.innerHTML += "<b>Autopay Discount: </b>$" + nAutoPay.toFixed(2);
          }
          if (rowMeta[eRow.showWork]){
            calcCost += rowMeta[eRow.showWork];
          } else {
            calcCost = "$" + cells[eTbl.monCost].innerHTML;
          }
          nTaxes = parseInt(OprMetaRow[eOprMeta.Taxes]);
          if (nTaxes) {calcCost += " + taxes";}
          moMonCost.innerText = calcCost;
          switch (nTaxes){
            case 1: sTaxes = "Point of sale taxes"; break;
            case 2: sTaxes = "Point of sale and telecom taxes"; break;
            default: sTaxes = "None";
          }
          moTaxes.innerText = sTaxes;
          moMins.innerText = cells[eTbl.mins].innerHTML;
          moTxts.innerText = cells[eTbl.txts].innerHTML;
          moData.innerText = cells[eTbl.data].innerHTML;
          nThrottled = rowMeta[eRow.overageThrottle];
          switch (nThrottled) {
            case -1:
              moThrotld.innerText = "No, hard capped";
              break;
            case 0:
              moThrotld.innerText = "N/A";
              break;
            default:
              moThrotld.innerText = "yes at " + nThrottled + " kbps";
          }

          if (rowMeta[eRow.MMS] === 1){
            moIosMms.innerText = 'Yes';
          }else{
            moIosMms.innerText = 'No';
          }

          if(rowMeta[eRow.AllowsHotspot]){
            var Hotspot_HS_Limit = rowMeta[eRow.Hotspot_HS_Limit];
            if (Hotspot_HS_Limit){
              if (Hotspot_HS_Limit == -1){
                moHotspot.innerText = "unlimited";
              }else if (Hotspot_HS_Limit >= 1024){
                moHotspot.innerText = Hotspot_HS_Limit / 1024 + " GB ";
              } else{
                moHotspot.innerText = rowMeta[eRow.Hotspot_HS_Limit] + " MB ";
              }
            }else{
              moHotspot.innerText = cells[eTbl.data].innerHTML;
            }
            if(rowMeta[eRow.Hotspot_HS_Throttle]){
              if (rowMeta[eRow.Hotspot_HS_Throttle] >= 1024){
                moHotspot.innerText += " at " + rowMeta[eRow.Hotspot_HS_Throttle]/1024 + "mbps";
              }else{
                moHotspot.innerText += " at " + rowMeta[eRow.Hotspot_HS_Throttle] + "kbps";
              }
            }else if (moHotspot.innerText != "None"){
              moHotspot.innerText += " at high speeds";
            }
            if(savedVals[ePersist.HotspotThrottle]){
              if (rowMeta[eRow.HotspotThrottle >= 1024]){
                moHotspot.innerText += " then unlimited at " + rowMeta[eRow.HotspotThrottle]/1024 + "mbps";
              }else{
                moHotspot.innerText += " then unlimited at " + rowMeta[eRow.HotspotThrottle] + "kbps";
              }
            }
            if(rowMeta[eRow.AllowsHotspot] > 1){
              moHotspot.innerText += " for $" + rowMeta[eRow.AllowsHotspot] + "/mo";
            }
          }else{
            moHotspot.innerText = "not supported";
          }
          var nVoiceRoaming = rowMeta[eRow.VoiceRoaming];
          var nTextRoaming = rowMeta[eRow.TextRoaming];
          var nDataRoaming = rowMeta[eRow.DataRoaming];
          if(nVoiceRoaming || nTextRoaming || nDataRoaming){
            if (nVoiceRoaming === 0){
                moRoaming.innerText = "mins: none, ";
            }else if (nVoiceRoaming == -1){
              moRoaming.innerText = "mins: " + cells[eTbl.mins].innerHTML +', ';
            }else if (nVoiceRoaming >1){
              moRoaming.innerText = "mins: " + nVoiceRoaming +",  ";
            }else if (nVoiceRoaming <1){
              moRoaming.innerText = "mins: " + nVoiceRoaming.toFixed(2) +"¢/ea, ";
            }
            if (nTextRoaming === 0){
              moRoaming.innerText += "txts: none, ";
            }else if (nTextRoaming == -1){
              moRoaming.innerText += "txts: " + cells[eTbl.txts].innerHTML +', ';
            }else if (nTextRoaming >1){
              moRoaming.innerText += "txts: " + nTextRoaming +", ";
            }else if (nTextRoaming <1){
              moRoaming.innerText += "txts: " + nTextRoaming.toFixed(2) * 100 +"¢/ea, ";
            }
            if (nDataRoaming === 0){
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
          moIsPayGo.innerText = parseInt(rowMeta[eRow.isPayGo]) ? "yes" : "no";
          moHasRollover.innerText = parseInt(rowMeta[eRow.HasRollover]) ? "yes" : "no";
          if (FmlyPlns[cells[eTbl.ID].innerHTML] !== undefined) {
            moFmlyPlns.innerHTML = "<b>Family Plans:</b> "
            + getFmlyPlns(cells[eTbl.ID].innerHTML);
          }else{
            moFmlyPlns.innerHTML = "";
          }
          moNotes.innerHTML = rowMeta[eRow.notes];
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

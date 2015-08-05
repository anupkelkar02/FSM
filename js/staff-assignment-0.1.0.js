/*


		Staff Assignments

*/




function StaffAssignment(idName, staff , schedule, workStatusList, replyStatusList, params) {
	
	
	this.loadParams = function(params) {
 		var defaultParams = {'width': 1200,
							 'height': 400,
							 'dayWidth': 30,
							 'dayHeight': 30,	
							 'titleWidth': 240,
							 'maxDayCount': 34,
							 'marginTop': 50,
							 'onScheduleChange': null,
							 'onScheduleAdd': null,
							 'onScheduleDelete': null,
							 'onCallClick': null,
                                                         'onSendCallClick': null	
						};
		if ( params ) {
			for ( var name in params ) {
				defaultParams[name] = params[name];
			}
		}
		return defaultParams;
	}
	
	this.initialize = function() {
		this.paperWidth = this.params.width;
		this.paperHeight = this.params.height;
		this.paperTop = 0;
		this.paperLeft = 0;
		this.zoomWidth = this.params.width;
		this.paper = Raphael(document.getElementById(this.idName), this.params.width, this.params.height);
		this.lateCount = 0;
	};
	
	this.initDraw = function () {
		this.paper.clear();
		this.paper.setViewBox(this.paperLeft, this.paperTop, this.zoomWidth, this.paperHeight, true);
		$('#'.idName).height = this.params.height;
		$('#'.idName).width = this.params.width;
		this.paper.renderfix();
		this.paper.safari();
	}
	
	this.loadScheduleDateRange = function() {
		this.startDate = new Date(0, 0, 0);
		this.endDate = new Date(0, 0, 0);
		var scheduleRow;
		for ( var index in this.schedule) {
			scheduleRow = this.schedule[index];
			if (scheduleRow.startDate > endDate ) {
				this.endDate = scheduleRow.startDate;
			}
		}
		this.startDate = this.endDate;
		for ( var index in this.schedule) {
			scheduleRow = this.schedule[index];
			if (scheduleRow.startDate < startDate ) {
				this.startDate = scheduleRow.startDate;
			}
		}
	}


	this.calcXPosFromDate = function(xStartPos, date) {
		var days = (date.getTime() - this.startDate.getTime() ) / (1000 * 60 * 60 * 24);
		return xStartPos + (days * this.params.dayWidth)
	}
	
	this.workStatusFromId = function(workStatusId) {
		var workStatus = null;
		for ( var index in this.workStatusList) {
			workStatus = this.workStatusList[index];
			if ( workStatus.workStatusId == workStatusId ) {
				return workStatus;
			}
		}
		return null;
	}

	this.replyStatusFromId = function(replyStatusId) {
		var replyStatus = null;
		for ( var index in this.replyStatusList) {
			replyStatus = this.replyStatusList[index];
			if ( replyStatus.replyStatusId == replyStatusId ) {
				return replyStatus;
			}
		}
		return null;
	}
	
	this.drawDateHeader = function(xPos)
	{
		var currentDate = new Date(this.startDate);
		var yPos = this.params.marginTop + 10;
		var xStartPos = xPos;
		var margin = 2;
		var dayCount = 0;
		while (currentDate < this.endDate && dayCount < this.params.maxDayCount) {
			xPos = this.calcXPosFromDate(xStartPos, currentDate);
			var boxItem = this.paper.rect(xPos + margin, yPos + margin, this.params.dayWidth  - (margin * 2), this.params.dayWidth - (margin * 2), 0);
			boxItem.attr('stroke-width', '0.5');
			if ( currentDate.getMonth() != this.startDate.getMonth() ) {
				boxItem.attr('fill', '#E0E0E0');
			}
			var textItem = this.paper.text(xPos + this.params.dayWidth / 2, yPos + this.params.dayWidth / 4, this.weekDayNames[currentDate.getDay()] );
			var textItem = this.paper.text(xPos + this.params.dayWidth / 2, yPos + this.params.dayWidth / 1.6, currentDate.getDate());
			currentDate.setDate( currentDate.getDate() + 1);
			dayCount ++;
		}
		yPos += 8;
		xPos = this.calcXPosFromDate(xStartPos, this.endDate);
		
		
//		var lineItem = self.paper.path('M' + '10' + ',' + (yPos  + this.params.dayWidth - 6) + 'L' + xPos + ',' + ( yPos + this.params.dayWidth - 6) )
//		lineItem.attr('stroke-opacity', '0.2');

	}
	
	
	this.drawScheduleBox = function (xPos, yPos, code, textColor, backgroundColor, flagColor, attendColor) {
		var margin = 2;
		var width = self.params.dayWidth;
		
		var boxSet = this.paper.set()
		var itemBox = self.paper.rect(xPos + margin, yPos + margin, width - (margin * 2), width - (margin * 2), 0);
		itemBox.attr('fill', backgroundColor);
		boxSet.push(itemBox);
		var textItem = this.paper.text(xPos + width / 2, yPos + width / 2, code);
		textItem.attr({'stroke': textColor, 'fill': textColor});
		boxSet.push(textItem);
		
		var flagMargin = margin * 6;
		var flagItem = this.paper.path('M' + (xPos + width - flagMargin - margin) +',' + (yPos + width - margin) + 'l' + flagMargin + ',-' + flagMargin + 'l0,' + flagMargin + 'l-' + flagMargin + ',0' );
		flagItem.attr({'stroke': textColor, 'fill': flagColor});
		if ( flagColor == null) {
			flagItem.hide();
		}
		boxSet.push(flagItem);
		
		var attendMargin = margin * 6;
		var attendItem = this.paper.path('M' + (xPos + margin ) +',' + (yPos + margin + attendMargin ) + 'l0,-' + attendMargin + 'l' + attendMargin + ',0l-' + attendMargin + ',' + attendMargin );
		attendItem.attr({'stroke': textColor, 'fill': attendColor});
		if ( attendColor == null) {
			attendItem.hide();
		}
		boxSet.push(attendItem);
		
		var emptyBox = this.paper.set();
		
		var crossMargin = 10;
		itemCross = this.paper.path('M' + (xPos + crossMargin) 	+ ',' + (yPos + crossMargin) + 'L' + ( xPos + (this.params.dayWidth - crossMargin)) + ',' + (yPos + (this.params.dayWidth - crossMargin)));
		itemCross.attr('stroke-width', '2');
		itemCross.attr('stroke-opacity', '0.2');
		emptyBox.push(itemCross);
		itemCross = this.paper.path('M' + ( xPos + (this.params.dayWidth - crossMargin)) 	+ ',' + (yPos + crossMargin) + 'L' + (xPos + crossMargin) + ',' + (yPos + (this.params.dayWidth - crossMargin)));
		itemCross.attr('stroke-width', '2');
		itemCross.attr('stroke-opacity', '0.2');
		emptyBox.push(itemCross);
		boxSet.push(emptyBox);
		if ( code == '' ) {
			emptyBox.show();
			boxSet.attr('stroke-opacity', '0.1');
		}
		else {
			emptyBox.hide();
		}

		return boxSet;	
	}
	
	
	this.findScheduleAtDate = function(startDate, staffId) {
		for ( var index in this.schedule) {
			var scheduleRow = this.schedule[index];
			if ( scheduleRow.staffId == staffId  && (scheduleRow.startDate - startDate) == 0) {
				return scheduleRow;
			}
		}
		return null;
	}
	this.drawSchedule = function(xStartPos, xPos, yPos, scheduleRow) {
		
		var workStatus = null;
		var replyStatus = null;
		var statusCode = '';
		var attendColor = null;
		var itemBox;
		if ( scheduleRow.workStatusId ) {
			workStatus = this.workStatusFromId(scheduleRow.workStatusId);
			statusCode = workStatus.code;
			if ( statusCode[0] == 'W' ) {
				statusCode = statusCode[1];
				var startTimeSeconds = 6 * 3600 * 1000;
				if ( statusCode == 'N' ) {
					startTimeSeconds = 18 * 3600 * 1000;
				}
				var today = new Date()
				var startTime = new Date(scheduleRow.startDate.getTime() + startTimeSeconds)
				var hoursLeft = (today - startTime) / (60 * 60 * 1000);
				if ( hoursLeft > 0 && hoursLeft < 10 && this.lateCount == 0) {
					attendColor = 'red';
					this.lateCount += 1;
					var staffItem = this.findStaff(scheduleRow.staffId);
					if ( staffItem ) {
						staffItem.isLate = true;
					}
				}
			}
			replyStatusColor = null;
			if ( scheduleRow.attendanceRequestTime ) {
				replyStatusColor = '#808080';
			}
			if ( scheduleRow.replyStatusId ) {
				replyStatus = this.replyStatusFromId(scheduleRow.replyStatusId);
				replyStatusColor = replyStatus.backgroundColor;
			}
			itemBox = this.drawScheduleBox(xPos, yPos, statusCode, workStatus.textColor, workStatus.backgroundColor, replyStatusColor, attendColor);
		}
		else {
			itemBox = this.drawScheduleBox(xPos, yPos, '', '#F0F0F0', '#FFFFFF');
		}

		
		var parent = this;
		itemBox.attr('cursor', 'hand');
		itemBox.data('xPos', xPos);
		itemBox.data('yPos', yPos);
		itemBox.data('workStatus', workStatus);
		itemBox.data('replyStatus', replyStatus);
		itemBox.data('scheduleRow', scheduleRow);
		itemBox.mousedown(function(t) {
			var xDiff = (this.data('xPos') - parent.keyGuide[0].attr('x')) + 2;
			var yDiff = 0;
			var workStatus = this.data('workStatus');
			var scheduleRow = this.data('scheduleRow');
			parent.selectCursor.show();
			parent.selectCursor.toBack();
			parent.selectCursor[0].animate({'x': this.data('xPos')}, 200);
			parent.selectCursor[1].animate({'y': this.data('yPos')}, 200);
			
			var workStatusCode = '';
			if ( workStatus) {
				workStatusCode = workStatus.code;
			}
			parent.moveKeyGuideCursorToWorkStatusCode(workStatusCode);
			parent.showKeyGuideStatus(scheduleRow.staffId, scheduleRow.startDate, workStatus, replyStatus);
			parent.selectedBoxItem = itemBox;
		});	
	}
	
	this.showKeyGuideStatus = function(staffId, startDate, workStatus, replyStatus) {
		staffItem = this.findStaff(staffId);
		this.keyGuideStatusDate.attr('text', startDate.toDateString());
		if ( staffItem ) {
			this.keyGuideName.attr('text', staffItem.title);
			if ( workStatus ) {
				if ( workStatus.title ) {
					this.keyGuideStatusTitle.attr('text', workStatus.title);
				}
				else {
					this.keyGuideStatusTitle.attr('text', 'Not Assigned');
				}
			}
			else {
				this.keyGuideStatusTitle.attr('text', 'Not Assigned');
			}
			if ( replyStatus ) {
				keyGuideStatusReplyStatus.attr('text', replyStatus.title)
			}
			else {
				keyGuideStatusReplyStatus.attr('text', '')
			}
		}
		else {
			this.keyGuideName.attr('text', '');
			this.keyGuideStatusTitle.attr('text', '');
		}
		this.keyGuideCallButtonSet[0].animate({'fill': '#0000A0'}, 1000);
		this.keyGuideCallButtonSet[1].animate({'fill': '#FFFFFF', 'stroke': '#FFFFFF'}, 1000);
		this.keyGuideSMSCallButtonSet[0].animate({'fill': '#0000A0'}, 1000);
		this.keyGuideSMSCallButtonSet[1].animate({'fill': '#FFFFFF', 'stroke': '#FFFFFF'}, 1000);
	}
	
	this.moveKeyGuideCursorToWorkStatusCode = function(workStatusCode) {
		var workStatusItem = this.keyGuideItemFromWorkStatusCode(workStatusCode);
		if ( workStatusItem ) {
			this.cursor.show();
			xDiff =  workStatusItem[0].attr('x') - this.cursor[0].attr('x');
			yDiff =  workStatusItem[0].attr('y') - this.cursor[0].attr('y');
			this.cursor.animate({'transform': 't' + xDiff + ',' + yDiff}, 200);
		}
	}
	
	this.drawStaffSchedule = function(xStartPos, yPos, staffId) {
		var currentDate = new Date(this.startDate);
		var dayCount = 0;
		while (currentDate < this.endDate && dayCount < this.params.maxDayCount) {
			var xPos = this.calcXPosFromDate(xStartPos, currentDate);
			var scheduleRow = this.findScheduleAtDate(currentDate, staffId);

			if ( scheduleRow == null) {
				scheduleRow = {'scheduleId': 0, 'staffId': staffId, 'startDate': new Date(currentDate), 'workStatusId': 0};
			}
			this.drawSchedule(xStartPos, xPos, yPos, scheduleRow);
			currentDate.setDate( currentDate.getDate() + 1);
			dayCount ++;
		}		
	}
	this.loadStaff = function() {
		this.fullTimeStaff = new Array();
		this.reliefStaff = new Array();
		for( var index in this.staff ) {
			var staffItem = this.staff[index];
			if ( staffItem.assignType == 'FullTime' ) {
				this.fullTimeStaff.push(staffItem);
			}
			else {
				this.reliefStaff.push(staffItem);
			}
		}
	}
	
	this.findStaff = function(staffId) {
		for ( var index in this.staff ) {
			staffItem = this.staff[index];
			if ( staffItem.staffId == staffId ) {
				return staffItem;
			}
		}
		return null;
	}
	this.drawStaffNames = function(xStartDay) {
		var yPos = this.params.marginTop;
		var assignType = '';
		var textItem;
		for( var index in this.staff ) {
			var staffItem = this.staff[index];
			staffItem.isLate = false;
			if ( staffItem.assignType != assignType ) {
				yPos += 22;
				textItem = this.paper.text(10, yPos, staffItem.assignType );
				textItem.attr('text-anchor', 'start');
				textItem.attr('font-size', '18');
				textItem.attr('font-weight', 'bold');
				assignType = staffItem.assignType;
				yPos += this.params.dayHeight;
			}
			staffItem.yPos = yPos;
			var title = staffItem.title
			textItem = this.paper.text(10, yPos + 4, title );
			textItem.attr('text-anchor', 'start');
			textItem.attr('font-size', '14');


			textItem = this.paper.text(xStartDay - 4, yPos + 4,  staffItem.phone_number);
			textItem.attr('text-anchor', 'end');
			textItem.attr('font-size', '10');

			this.drawStaffSchedule(xStartDay, yPos - 10, staffItem.staffId);
			var gridWidth = this.params.titleWidth + (this.params.maxDayCount * this.params.dayWidth);
			var lineItem = self.paper.path('M' + 10 + ',' + ( yPos - 10 ) + 'l' +  (gridWidth - 10) + ',0' )
			lineItem.attr('stroke-opacity', '0.2');
			
			if ( staffItem.isLate ) {
				textItem = this.paper.text(160, yPos + 4, 'Late' );
				textItem.attr({'text-anchor': 'start', 'font-size': '10', 'stroke': 'red', 'fill': 'red'});
			}
			
			yPos += this.params.dayHeight;
			
		}
		var lineItem = self.paper.path('M' + 10 + ',' + ( yPos - 10 ) + 'l' + (gridWidth - 10) + ',0')
		lineItem.attr('stroke-opacity', '0.2');
		var currentDate = new Date(this.startDate);
		while ( currentDate < this.endDate ) {
			xPos = this.calcXPosFromDate(xStartDay, currentDate);
			var lineItem = self.paper.path('M' + xPos + ',' + (this.params.marginTop + this.params.dayHeight + 12) + 'L' + xPos + ',' + (yPos - 10))
			lineItem.attr('stroke-opacity', '0.2');
			currentDate.setDate( currentDate.getDate() + 1);			
		}
	}
	
	this.drawCursor = function(xPos, yPos, dayWidth, height)
	{
		var cursorColor = 'blue';
		var cursorItem;
		var cursorSet = this.paper.set();


		cursorItem = this.paper.rect(xPos  + 2, yPos  + 2, dayWidth - 4, dayWidth - 4);
		cursorItem.attr({'stroke': cursorColor, 'stroke-width': 4});		
		cursorSet.push(cursorItem);

		cursorItem = this.paper.path('M' + xPos + ',' + (yPos - 8) + 'l' + ( dayWidth / 2) + ',8' + 'l' + ( dayWidth / 2) + ',-8' + 'l' + '-' + this.params.dayWidth + ',0');
		cursorItem.attr({'fill': cursorColor, 'stroke': 'black'});
		cursorSet.push(cursorItem);
		cursorItem = this.paper.path('M' + xPos + ',' + (yPos + height - 8) + 'l' + ( dayWidth / 2) + ',-8' + 'l' + ( dayWidth / 2) + ',8' + 'l' + '-' + this.params.dayWidth + ',0');
		cursorItem.attr({'fill': cursorColor, 'stroke': 'black'});
		cursorSet.push(cursorItem);
		
		//cursorItem = this.paper.rect(xPos, yPos - 8, dayWidth, dayWidth + 16);
		//cursorItem.attr({'stroke': 'red', 'stroke-width': 1});		
		//cursorSet.push(cursorItem);
		
		cursorSet.toFront();
		return cursorSet;

	}

	this.keyGuideItemFromWorkStatusCode = function(code) {
		var index;
		//var workStatusBoxSet = this.keyGuide[2];
		for ( index = 0; index < this.workStatusBoxSet.length; index ++ ) {
			var workStatus = this.workStatusBoxSet[index][0].data('workStatus')
			if ( workStatus ) {
				if ( workStatus.code == code ) {
					return this.workStatusBoxSet[index];
				}
			}
		}
		return null;
	}
	this.loadKeyGuide = function() {
		var height = this.params.dayHeight + 16;
		var titleWidth = this.params.titleWidth + (this.params.dayWidth * 6);
		var statusBarWidth = (this.workStatusList.length + 1) * (this.params.dayWidth);
		var width = statusBarWidth + 10 + titleWidth;
		var xPos = this.paper.width - width;
		xPos = 0;
		var yPos = this.paper.height - height - 4;
		yPos = 4;
	
		var keyGuideSet = this.paper.set();
		if ( this.workStatusList.length > 0 ) {
			var borderItem = this.paper.rect(xPos, yPos, width, height, 2);
			borderItem.attr({fill: "#F0F0F0", stroke: '#808080'});
			
			keyGuideSet.push(borderItem);
			this.keyGuideName = this.paper.text( xPos + 4, yPos + 10, "Name");
			this.keyGuideName.attr( {'text-anchor': 'start', 'font-size': '14'});
			keyGuideSet.push(this.keyGuideName);


			this.keyGuideStatusDateLabel = this.paper.text( xPos + 4, yPos + 30, "Date:");
			keyGuideStatusDateLabel.attr( {'text-anchor': 'start', 'font-size': '14', 'font-weight': 'bold'});
			keyGuideSet.push(keyGuideStatusDateLabel);

			this.keyGuideStatusDate = this.paper.text( xPos + 40, yPos + 30, "");
			this.keyGuideStatusDate.attr( {'text-anchor': 'start', 'font-size': '14'});
			keyGuideSet.push(this.keyGuideStatusDate);

			this.keyGuideStatusTitle = this.paper.text( xPos + titleWidth - 4, yPos + 30, "Status");
			this.keyGuideStatusTitle.attr( {'text-anchor': 'end', 'font-size': '14', 'font-weight': 'bold'});
			keyGuideSet.push(this.keyGuideStatusTitle);


			keyGuideStatusReplyStatusLabel = this.paper.text( xPos  + 180, yPos + 30, "Reply:");
			keyGuideStatusReplyStatusLabel.attr( {'text-anchor': 'start', 'font-size': '12', 'font-weight': 'bold'});
			keyGuideSet.push(keyGuideStatusReplyStatusLabel);

			this.keyGuideStatusReplyStatus = this.paper.text( xPos  + 220, yPos + 30, "");
			this.keyGuideStatusReplyStatus.attr( {'text-anchor': 'start', 'font-size': '12'});
			keyGuideSet.push(this.keyGuideStatusReplyStatus);


			xPos += titleWidth;
			yPos += 8;
			this.cursor = drawCursor(xPos, yPos, this.params.dayWidth, height);
			keyGuideSet.push(this.cursor);
			this.workStatusBoxSet = this.paper.set();
			boxItem = this.drawScheduleBox(xPos, yPos, '', '#000', '#FFF');
			boxItem.attr('cursor', 'hand');
			boxItem.data('workStatus', {code: ''});
			boxItem.click(this.onKeyGuideBoxClick);
			this.workStatusBoxSet.push(boxItem);
			xPos += this.params.dayWidth;

			for ( var index in this.workStatusList ) {
				var workStatus = this.workStatusList[index];
				var statusCode = workStatus.code;
				if ( statusCode[0] == 'W' ) {
					statusCode = statusCode[1];
				}
				boxItem = this.drawScheduleBox(xPos, yPos, statusCode, workStatus.textColor, workStatus.backgroundColor);
				boxItem.attr('cursor', 'hand');
				boxItem.data('workStatus', workStatus);
				parent = this;
				boxItem.click(this.onKeyGuideBoxClick);
				this.workStatusBoxSet.push(boxItem);
				xPos += this.params.dayWidth;
			}
			keyGuideSet.push(this.workStatusBoxSet);
			
			
			this.keyGuideCallButtonSet = this.paper.set();
			var buttonRect = this.paper.rect( xPos + 30, yPos, 60, 30, 5);
			buttonRect.attr({'storke': '#000000', 'fill': '#A0A0A0'});
			this.keyGuideCallButtonSet.push(buttonRect);
			
			var buttonText = this.paper.text( xPos + 60, yPos + 15, "Call");
			buttonText.attr({ 'fill': '#808080', 'stroke': '#808080', 'text-anchor': 'center', 'font-size': 12 });
			this.keyGuideCallButtonSet.push(buttonText);
			this.keyGuideCallButtonSet.attr('cursor', 'hand');
			keyGuideSet.push(this.keyGuideCallButtonSet);
                        parent = this;
			this.keyGuideCallButtonSet.click(function(event) {
				scheduleRow = parent.selectedBoxItem[0].data('scheduleRow');
				if ( parent.params.onSendCallClick  ) {
					parent.params.onSendCallClick(scheduleRow.scheduleId,'Voice');
					var xPos = parent.keyGuideCallButtonSet.attr('x')
					parent.keyGuideCallButtonSet.animate({'transform': 't1,1'}, 100, 'linear', function () {
						parent.keyGuideCallButtonSet.animate({'transform': 't-1,-1'}, 100, 'linear');
					});
				}				
			});
			this.keyGuideSMSCallButtonSet = this.paper.set();
			buttonRect = this.paper.rect( xPos +100, yPos, 60, 30, 5);
			buttonRect.attr({'storke': '#000000', 'fill': '#A0A0A0'});
			this.keyGuideSMSCallButtonSet.push(buttonRect);
			
			buttonText = this.paper.text( xPos + 130, yPos + 15, "SMS");
			buttonText.attr({ 'fill': '#808080', 'stroke': '#808080', 'text-anchor': 'center', 'font-size': 12 });
			this.keyGuideSMSCallButtonSet.push(buttonText);
			this.keyGuideSMSCallButtonSet.attr('cursor', 'hand');
			keyGuideSet.push(this.keyGuideSMSCallButtonSet);
			parent = this;
			this.keyGuideSMSCallButtonSet.click(function(event) {
				scheduleRow = parent.selectedBoxItem[0].data('scheduleRow');
				if ( parent.params.onCallClick  ) {
					parent.params.onCallClick(scheduleRow.scheduleId,"sms");
					var xPos = parent.keyGuideSMSCallButtonSet.attr('x')
					parent.keyGuideSMSCallButtonSet.animate({'transform': 't1,1'}, 100, 'linear', function () {
						parent.keyGuideSMSCallButtonSet.animate({'transform': 't-1,-1'}, 100, 'linear');
					});
				}				
			});

		}
		this.keyGuide = keyGuideSet;
		this.keyGuide.toFront();
	}
	
	this.drawSelectBox = function() {
		var selectBox = this.drawScheduleBox(10, 0, '1', '#FFFFFF', '#0000F0');
		selectBox[0].attr({'stroke-width': '2'});
			
		return selectBox;
	}
	
	this.onKeyGuideBoxClick = function(event) {
		if ( parent.selectedBoxItem ) {
			workStatus = this.data('workStatus');
			replyStatus = this.data('replyStatus');
			scheduleRow = parent.selectedBoxItem[0].data('scheduleRow');
			parent.showKeyGuideStatus(scheduleRow.staffId, scheduleRow.startDate, workStatus, replyStatus);
			parent.moveKeyGuideCursorToWorkStatusCode(workStatus.code);	
			var workStatusCode = workStatus.code;
			if ( workStatusCode[0] == 'W' ) {
				workStatusCode = workStatusCode[1];
			}
			
			if ( workStatus.code == '' && scheduleRow.scheduleId ) {
				if ( parent.params.onScheduleDelete  ) {
					parent.params.onScheduleDelete(scheduleRow.scheduleId);
					scheduleRow.scheduleId = 0;			
				}
				parent.selectedBoxItem[0].attr({'fill': '#FFF'});
				parent.selectedBoxItem[1].attr({'text': '', 'fill': '#000' ,'stroke': '#000'});
				parent.selectedBoxItem.attr('stroke-opacity', '0.1');
				parent.selectedBoxItem.data('workStatus', {code: ''});
				parent.selectedBoxItem[3].show();
			}
			else if ( workStatus.code != '' && scheduleRow.scheduleId == 0 ) {
				if ( parent.params.onScheduleAdd ) {
					scheduleRow.scheduleId = parent.params.onScheduleAdd(scheduleRow.staffId, scheduleRow.startDate, workStatus.workStatusId);					
					scheduleRow.workStatusId = workStatus.workStatusId;
				}
				parent.selectedBoxItem[0].attr({'fill': workStatus.backgroundColor});
				parent.selectedBoxItem[1].attr({'text': workStatusCode, 'fill': workStatus.textColor, 'stroke': workStatus.textColor});
				parent.selectedBoxItem.attr('stroke-opacity', '1');
				parent.selectedBoxItem.data('workStatus', workStatus);
				parent.selectedBoxItem[3].hide();
			}
			else if ( workStatus.code != '' && scheduleRow.scheduleId != 0 ) {
				if ( parent.params.onScheduleChange ) {
					parent.params.onScheduleChange(scheduleRow.scheduleId, workStatus.workStatusId);
				}
				parent.selectedBoxItem[0].attr({'fill': workStatus.backgroundColor});
				parent.selectedBoxItem[1].attr({'text': workStatusCode, 'fill': workStatus.textColor, 'stroke': workStatus.textColor});
				parent.selectedBoxItem.data('workStatus', workStatus);
			}
			
		}
	}
	
	this.drawSelectCursor = function () {
		var selectSet = this.paper.set();
		// vertical
		var item = this.paper.rect(0, 10  + this.params.marginTop, this.params.dayWidth, 
								(this.params.dayHeight * (this.fullTimeStaff.length + this.reliefStaff.length + 3)) - 5, 4 );
		selectSet.push(item);
		// horizontal
		item = this.paper.rect(5, this.params.dayHeight + this.params.marginTop + 10, this.params.titleWidth + (this.params.dayWidth * this.params.maxDayCount) - 4, this.params.dayHeight, 4 );
		selectSet.push(item);
		selectSet.attr({'fill': 'blue', 'fill-opacity': '0.05', 'stroke': 'blue', 'stroke-opacity': '1'});

		return selectSet;
		
	}
	this.onKeyPress = function(event) {
		console.log(event);
	}

	
	this.idName = idName;
	this.staff = staff
	this.staff.sort(function( a, b) {
		if ( a.assignType > b.assignType ) {
			return 1;
		}
		if ( a.assignType < b.assignType ) {
			return -1;
		}
		return 0;
	});

	
	this.selectedBoxItem = null;
	this.weekDayNames = new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	this.schedule = schedule;
	this.workStatusList = workStatusList;
	this.replyStatusList = replyStatusList
	this.params = this.loadParams(params);

	this.loadStaff();
	this.params.height = (this.fullTimeStaff.length + this.reliefStaff.length + 5) * this.params.dayHeight;
	this.params.width = this.params.titleWidth + (this.params.dayWidth * this.params.maxDayCount + 10);
	initialize();
	initDraw();
	loadScheduleDateRange();
	
	loadKeyGuide();
	if ( this.cursor) {
		this.cursor.hide();
	}
	this.selectCursor = this.drawSelectCursor()
	this.selectCursor.hide();
	
	drawDateHeader(this.params.titleWidth);
	drawStaffNames(this.params.titleWidth);
	
}


import React, { ComponentProps, useState } from 'react';
import {
	Calendar as Cal,
	Messages,
	momentLocalizer,
	Views,
	View,
	stringOrDate,
} from 'react-big-calendar';
import moment from 'moment/moment';
import withDragAndDrop, {
	withDragAndDropProps,
} from 'react-big-calendar/lib/addons/dragAndDrop';
import 'moment/locale/cs';
import 'react-big-calendar/lib/css/react-big-calendar.css';
import 'react-big-calendar/lib/addons/dragAndDrop/styles.css';

export default function Calendar(
	args: Omit<
		ComponentProps<typeof Cal> & withDragAndDropProps<object, object>,
		'localizer' | 'culture' | 'messages'
	>
) {
	moment.locale('cs');
	const localizer = momentLocalizer(moment);
	const Cale = withDragAndDrop(Cal);
	const messages: Messages = {
		previous: 'Předchozí',
		next: 'Další',
		yesterday: 'Včera',
		today: 'Dnes',
		tomorrow: 'Zítra',
		day: 'Den',
		week: 'Týden',
		month: 'Měsíc',
		work_week: 'Pracovní týden',
		agenda: 'Seznam',
		time: 'Čas',
		date: 'Datum',
		allDay: 'Celý den',
		event: 'Událost',
		noEventsInRange: 'Žádné události v tomto rozmezí',
		showMore: (total) => `Zobrazit další (${total})`,
	};
	const [view, setView] = useState<View>(Views.DAY);
	const [date, setDate] = useState<stringOrDate>();
	return (
		<Cale
			{...args}
			messages={messages}
			culture='cs'
			localizer={localizer}
			style={{ height: '80vh' }}
			defaultView={view}
			onView={(view) => setView(view)} /* 
			onRangeChange={(range) => console.log(range)} */
			onNavigate={(date) => setDate(date)}
			date={date}
		/>
	);
}

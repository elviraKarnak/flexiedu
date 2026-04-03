document.addEventListener('DOMContentLoaded', function() {

    var calendarEl = document.getElementById('live-classes-calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {

        height: '400',
        width: 'auto',
        contentHeight: 'auto',
        expandRows: true,

        eventDisplay: 'block',          // ✅ FIX
        dayMaxEventRows: 3,             // ✅ FIX

        eventContent: function(arg) {   // ✅ FIX
            return {
                html: `
                    <div style="font-size:12px; line-height:1.3;">
                        <strong>${arg.timeText}</strong><br>
                    </div>
                `
            };
        },

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        initialView: 'dayGridMonth',

        events: "/wp-admin/admin-ajax.php?action=get_live_classes",

        eventClick: function(info) {
            
            info.jsEvent.preventDefault();

            let event = info.event;

            document.getElementById('liveClassModalTitle').innerText = event.title;

            let start = formatDateTime(event.start);
            let end = event.end ? formatDateTime(event.end) : '';

            document.getElementById('liveClassModalTime').innerText =
                start + (end ? ' - ' + end : '');

            let webinar = event.extendedProps.webinar_link;
            let joinBtn = document.getElementById('liveClassModalJoinBtn');

            let now = new Date();
            let eventEnd = event.end ? new Date(event.end) : null;

            if (webinar) {
                joinBtn.href = webinar;

                // Check if event expired
                if (eventEnd && now > eventEnd) {
                    joinBtn.classList.add('disabled');
                    joinBtn.style.pointerEvents = 'none';
                    joinBtn.innerText = 'Class Ended';
                } else {
                    joinBtn.classList.remove('disabled');
                    joinBtn.style.pointerEvents = 'auto';
                    joinBtn.innerText = 'Join Class';
                }

                joinBtn.style.display = 'inline-block';
            } else {
                joinBtn.style.display = 'none';
            }

            var modal = new bootstrap.Modal(document.getElementById('liveClassModal'));
            modal.show();
        }
    });

    calendar.render();
});

function formatDateTime(dateStr) {

    let date = new Date(dateStr);

    let day = date.getDate();
    let month = date.toLocaleString('en-IN', { month: 'long' });
    let year = date.getFullYear().toString().slice(-2);

    let time = date.toLocaleString('en-IN', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });

    return `${day} ${month} ${year} ${time}`;
}
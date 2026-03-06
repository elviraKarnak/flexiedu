// cspell:ignore irCommunication

import '../scss/student-communication.scss';
import irCommunication from './communication';

const ircommunication = new irCommunication;
const msg_data = new irCommunication(ir_communication_loc.has_doubts);

ircommunication.toggleMaximize();
ircommunication.toggleMinimize();
ircommunication.closeElement('.ir-close', '.ir-msg-box');
ircommunication.closeElement('.ir-close-note', '.ir-ask-doubts');
ircommunication.closeElement('.ir-close-note', '.ir-ask-doubts + .ir-message-notification');
ircommunication.closeElement('.ir-close', '.ir-ask-doubts');
ircommunication.closeElement('.ir-close', '.ir-ask-doubts + .ir-message-notification');
ircommunication.closeElement('.ir-minimize', '.ir-ask-doubts');
ircommunication.closeElement('.ir-minimize', '.ir-ask-doubts + .ir-message-notification');
ircommunication.closeElement('.ir-minimize', '.ir-question-mark');
ircommunication.closeElement('.ir-maximize', '.ir-ask-doubts');
ircommunication.closeElement('.ir-maximize', '.ir-ask-doubts + .ir-message-notification');
ircommunication.closeElement('.ir-maximize', '.ir-question-mark');
ircommunication.closeElement('.irc-icon-Close', '.ir-msg-toast');
ircommunication.showElement('.ir-close', '.ir-question-mark', 'block', msg_data);
ircommunication.showElement('.ir-question-mark', '.ir-ask-doubts', 'block', msg_data);
ircommunication.showElement('.ir-send-doubts', '.ir-msg-box', 'flex', msg_data);
ircommunication.showElement('.ir-message-doubts button', '.ir-msg-box', 'flex', msg_data);
ircommunication.showElement('.ir-msg-new-doubts button', '.ir-msg-box', 'flex', msg_data);
ircommunication.showMsgPopup(msg_data);
ircommunication.disableButton();

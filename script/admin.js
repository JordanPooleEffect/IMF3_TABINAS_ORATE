function showMessageBox() {
    document.getElementById('messageBox').style.display = 'block';
}

function hideMessageBox() {
    document.getElementById('messageBox').style.display = 'none';
}

function showUpdateBox() {
    document.getElementById('messageBoxUpdate').style.display = 'block';
}

function hideUpdateBox() {
    document.getElementById('messageBoxUpdate').style.display = 'none';
}

function showUpdateAnswerBox() {
    document.getElementById('messageBoxAnswers').style.display = 'block';
}

function hideUpdateAnswerBox() {
    document.getElementById('messageBoxAnswers').style.display = 'none';
}

function showDeleteConfirmation(userId) {
    document.getElementById('deleteUserId').value = userId;
    showMessageBox();
}

function confirmUpdate(userId) {
    var updateUserId = document.getElementById('updateUserId');
    updateUserId.value = userId;

    showUpdateBox();

    return false;
}

function confirmAnswer(answerId) {
    document.getElementById('updateUserIdAnswers').value = answerId;
    document.getElementById('AnswerTextInput').value = document.getElementById('answerText_' + answerId).innerText;
    showUpdateAnswerBox();
}
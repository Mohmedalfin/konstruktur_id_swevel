let profileState = {
    isLoading: true,
    isSaving: false,
    isEditMode: false,
    isPasswordEditMode: false,
    error: null,
    data: null,
    snapshot: {},
};

export function getState() {
    return profileState;
}

export function setState(newState) {
    profileState = { ...profileState, ...newState };
}

export function setProfileData(data) {
    profileState.data = data;
}

export function setSnapshot(snapshot) {
    profileState.snapshot = { ...snapshot };
}

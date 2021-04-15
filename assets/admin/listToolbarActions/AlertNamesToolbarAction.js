import {translate} from 'sulu-admin-bundle/utils';
import {AbstractListToolbarAction} from 'sulu-admin-bundle/views';

export default class AlertNamesToolbarAction extends AbstractListToolbarAction {
    getToolbarItemConfig() {
        const {disable_for_empty_selection: disableForEmptySelection = false} = this.options;

        return {
            type: 'button',
            label: translate('app.alert_names'),
            icon: 'su-bell',
            disabled: disableForEmptySelection && this.listStore.selections.length === 0,
            onClick: this.handleClick,
        };
    }

    handleClick = () => {
        if (this.listStore.selections.length === 0) {
            alert(translate('app.no_contacts_selected'));

            return;
        }

        const selectedNames = this.listStore.selections
            .map((item) => item.firstName + ' ' + item.lastName)
            .join('\n');

        alert(translate('app.selected_contacts') + ':\n' + selectedNames);
    };
}

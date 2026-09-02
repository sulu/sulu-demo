import {translate} from 'sulu-admin-bundle/utils';
import {AbstractListItemAction} from 'sulu-admin-bundle/views';

export default class AlertNameItemAction extends AbstractListItemAction {
    getItemActionConfig(item) {
        const {disabled_ids: disabledIds = []} = this.options;

        return {
            icon: 'su-bell',
            disabled: item ? disabledIds.includes(item['id']) : false,
            onClick: item ? () => this.handleClick(item) : undefined,
        };
    }

    handleClick = (item) => {
        alert(translate('app.clicked_contact') + ':\n' + item.firstName + ' ' + item.lastName);
    };
}

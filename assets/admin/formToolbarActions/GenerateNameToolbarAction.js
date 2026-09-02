import {translate} from 'sulu-admin-bundle/utils';
import {AbstractFormToolbarAction} from 'sulu-admin-bundle/views';

const FIRSTNAMES = ['James', 'Mary', 'John', 'Patricia', 'Robert', 'Jennifer', 'Michael', 'Linda']
const LASTNAMES = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Garcia']

export default class GenerateNameToolbarAction extends AbstractFormToolbarAction {
    getToolbarItemConfig() {
        const {allow_overwrite: allowOverwrite = false} = this.options;
        const formData = this.resourceFormStore.data;
        const nameAlreadySet = !!formData.firstName || !!formData.lastName;

        return {
            type: 'button',
            label: translate('app.generate_name'),
            icon: 'su-magic',
            disabled: !allowOverwrite && nameAlreadySet,
            onClick: this.handleClick,
        };
    }

    handleClick = () => {
        const randomFirstName = FIRSTNAMES[Math.floor(Math.random() * FIRSTNAMES.length)];
        this.resourceFormStore.change('firstName', randomFirstName);

        const randomLastName = LASTNAMES[Math.floor(Math.random() * LASTNAMES.length)];
        this.resourceFormStore.change('lastName', randomLastName);
    };
}

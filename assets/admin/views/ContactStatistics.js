import React from 'react';
import {observer} from 'mobx-react';
import {action, observable} from 'mobx';
import {Loader, Button} from 'sulu-admin-bundle/components';
import {withToolbar} from 'sulu-admin-bundle/containers';
import {translate} from 'sulu-admin-bundle/utils';
import {ResourceRequester} from 'sulu-admin-bundle/services';

@observer
class ContactStatistics extends React.Component {
    @observable loading = false;
    @observable contactCount = undefined;

    componentDidMount() {
        this.loadData();
    }

    @action loadData = () => {
        this.loading = true;
        this.contactCount = undefined;
        const contactResourceKey = this.props.router.route.options.contactResourceKey;

        return ResourceRequester.getList(contactResourceKey)
            .then(action((response) => {
                this.contactCount = response.total;
            }))
            .catch((e) => {
                console.error('Error while loading contact statistics from server.', e);
            })
            .finally(action(() => {
                this.loading = false;
            }));
    }

    navigateToContactList = () => {
        const contactListView = this.props.router.route.options.contactListView;

        this.props.router.navigate(contactListView);
    }

    render() {
        if (this.loading) {
            return <Loader/>;
        }

        return (
            <div>
                <div style={{marginBottom: 20}}>
                    {translate('app.contact_statistics_count_text', {contactCount: this.contactCount})}
                </div>
                <Button
                    onClick={this.navigateToContactList}
                    skin="link"
                >
                    {translate('app.contact_statistics_link_text')}
                </Button>
            </div>
        );
    }
}

export default withToolbar(ContactStatistics, function () {
    return {
        items: [
            {
                type: 'button',
                label: translate('app.refresh_statistics'),
                icon: 'su-sync',
                disabled: this.loading,
                onClick: () => {
                    this.loadData();
                },
            }
        ]
    };
});

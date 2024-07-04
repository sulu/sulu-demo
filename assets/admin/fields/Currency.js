import React from 'react';
import {Number} from 'sulu-admin-bundle/components';

class Currency extends React.Component{
    handleInputChange = (floatAmount) => {
        const centAmount = floatAmount !== undefined ?
            Math.floor(floatAmount * 100)
            : undefined;

        const allowNegativeAmount = this.props.schemaOptions.allowNegativeAmount !== undefined
            ? this.props.schemaOptions.allowNegativeAmount.value
            : false;

        const normalizedCentAmount = !allowNegativeAmount && centAmount !== undefined
            ? Math.max(0, centAmount)
            : centAmount;

        this.props.onChange(normalizedCentAmount);
        this.props.onFinish();
    };

    render() {
        const {dataPath, disabled, error, value: centAmount} = this.props;

        const floatAmount = centAmount !== undefined
            ? centAmount / 100
            : undefined;

        return (
            <Number
                disabled={!!disabled}
                icon="su-dollar"
                id={dataPath}
                onChange={this.handleInputChange}
                step={0.01}
                valid={!error}
                value={floatAmount}
            />
        );
    }
}

export default Currency;

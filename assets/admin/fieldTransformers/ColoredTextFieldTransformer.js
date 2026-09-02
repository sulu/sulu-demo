import React from 'react';

const COLORS = ['gray', 'maroon', 'red', 'purple', 'green', 'blue', 'teal', 'orange', 'goldenrod', 'crimson']

export default class ColoredTextFieldTransformer {
    transform(value) {
        const randomColor = COLORS[Math.floor(Math.random() * COLORS.length)];

        return (
            <div style={{color: randomColor}}>
                {value}
            </div>
        );
    }
}

import React, {Component, Fragment} from 'react';
import PropTypes from 'prop-types';

class EditFieldModal extends Component {
    constructor(props) {
        super(props);
        this.state = {
            modal: false,
            enLabelName: '',
            arLabelName: '',
            fieldType: '',
            validationType: '',
            textFiledValue: '',
            require: false,
            multipleOption: false,
            enOptionValues: [],
            arOptionValues: [],
            editFieldData: this.props.editFieldData,
            errorMsg: false,
            option: [],
        };
    }

    componentDidMount() {
        0 < this.state.editFieldData.length && this.state.editFieldData.map((item, index) => {
            const enValues = item.en.values;
            const arValues = item.ar.values;
            if (undefined !== enValues && 0 < enValues.length) {
                let enOptionValues = [];
                let arOptionValues = [];
                let option = [];
                for (let i = 0; i < enValues.length; i++) {
                    enOptionValues[i] = enValues[i].value;
                    arOptionValues[i] = arValues[i].value;
                    option.push({
                        'en': {'label': `Option${i + 1} (English)`},
                        'ar': {'label': `Option${i + 1} (Arabic)`}
                    });
                }
                this.setState({enOptionValues, arOptionValues, option});
            }
        });
    }

    handleChange = (event) => {
        let editFieldData = this.state.editFieldData;

        switch ( event.target.name ) {
            case 'enLabelName':
                editFieldData[0].en.label = event.target.value;
                break;

            case 'arLabelName':
                editFieldData[0].ar.label = event.target.value;
                break;

            case 'enButtonLabel1':
                editFieldData[0].en.button1 = event.target.value;
                break;

            case 'arButtonLabel1':
                editFieldData[0].ar.button1 = event.target.value;
                break;
        }

        this.setState({
            editFieldData: editFieldData
        });

    };

    onSelectValidationType = (event) => {
        event.preventDefault();
        let editFieldData = this.state.editFieldData;

        editFieldData[0].en.type = event.target.value;
        editFieldData[0].ar.type = event.target.value;

        this.setState({
            editFieldData: editFieldData
        });

    };

    handleModalClose = (event) => {
        this.props.handleEditModelClose();
    };

    camelCase = (str) => {
        return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function (word, index) {
            return 0 === index ? word.toLowerCase() : word.toUpperCase();
        }).replace(/\s+/g, '');
    };

    handleRequiredCheck = (event) => {
        let editFieldData = this.state.editFieldData;

        editFieldData[0].en.required = event.target.checked;
        editFieldData[0].ar.required = event.target.checked;

        this.setState({
            editFieldData: editFieldData
        });
    };

    handleAllowMultiple = (event) => {
        let editFieldData = this.state.editFieldData;

        editFieldData[0].en.multiple = event.target.checked;
        editFieldData[0].ar.multiple = event.target.checked;

        this.setState({
            editFieldData: editFieldData
        });
    };

    handleSaveNewField = (event) => {
        const enArr          = this.state.editFieldData[0].en;
        const arArr          = this.state.editFieldData[0].ar;
        const option         = this.state.option;
        const enOptionValues = this.state.enOptionValues;
        const arOptionValues = this.state.arOptionValues;

        this.setState({errorMsg: false});
        // TODO: If we'll use switch operator - the code will be clearer
        if ('' !== enArr.label && '' !== arArr.label) {
            if ('Text Input' === enArr.control) {
                if ('' !== enArr.type) {
                    this.props.handleEditField(this.state.editFieldData);
                } else {
                    this.setState({errorMsg: true});
                }
            } else if ('Text Area' === enArr.control) {
                this.props.handleEditField(this.state.editFieldData);
            } else if ('Dropdown Select' === enArr.control || 'Radio' === enArr.control || 'Checkbox' === enArr.control) {
                let optionValueErrorCount = 0;
                if (0 < enArr.values.length && 0 < arArr.values.length) {
                    0 < option.length && option.map(( item, index ) => {
                        if( '' === enOptionValues[index] || index >= enOptionValues.length || undefined === enOptionValues[index]){
                            optionValueErrorCount++;
                        }
                        if( '' === arOptionValues[index] || index  >= arOptionValues.length  || undefined === arOptionValues[index] ){
                            optionValueErrorCount++;
                        }

                    });

                    if( 0 === optionValueErrorCount ){
                        this.props.handleEditField(this.state.editFieldData);
                    }else{
                        this.setState({errorMsg: true});
                    }
                } else {
                    this.setState({errorMsg: true});
                }
            } else if ('File Upload' === enArr.control) {
                const uploadOptions = enArr.uploadOptions;
                let allowedOption = [];
                for (let key in uploadOptions) {
                    if (uploadOptions.hasOwnProperty(key)) {
                        uploadOptions[key] && allowedOption.push(key);
                    }
                }
                if ('' !== enArr.button1 && '' !== arArr.button1 && 0 < allowedOption.length) {
                    this.props.handleEditField(this.state.editFieldData);
                } else {
                    this.setState({errorMsg: true});
                }
            }
        } else {
            this.setState({errorMsg: true});
        }

    };


    addNewOptionHandle = () => {
        const optionLength = this.state.option.length;
        let option = this.state.option;
        option.push({
            'en': {'label': `Option${optionLength + 1} (English)`},
            'ar': {'label': `Option${optionLength + 1} (Arabic)`}
        });
        $('.multiple-option-group').animate({scrollTop: $('.option-wrap:last').offset().top}, 500);
        this.setState({option});
    };

    // TODO: Try tto add comments in your code. The general rule - your code should understandable for all new people in a team.. Read book "Чистый код_ создание, анализ и рефакторинг ( PDFDrive ).pdf"
    deleteOption = (event) => {
        const currentIndex = event.currentTarget.attributes.getNamedItem('index').value;
        let editFieldData = this.state.editFieldData;
        const enOptionValues = this.state.enOptionValues.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex));
        const arOptionValues = this.state.arOptionValues.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex));
        const enEditFieldDataValue = 0 < editFieldData[0].en.values.length && editFieldData[0].en.values.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex));
        const arEditFieldDataValue = 0 < editFieldData[0].ar.values.length && editFieldData[0].ar.values.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex));
        editFieldData[0].en.values = enEditFieldDataValue;
        editFieldData[0].ar.values = arEditFieldDataValue;
        this.setState({option: this.state.option.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex))});
        this.setState({enOptionValues, arOptionValues, editFieldData});
    };

    handleOptionChange = (i, langaugeType, event) => {
        let editFieldData = this.state.editFieldData;
        let enValue = editFieldData[0].en.values;
        let arValue = editFieldData[0].ar.values;
        if ('en' === langaugeType) {
            let optionValues = [...this.state.enOptionValues];
            optionValues[i] = event.target.value;

            if (i < enValue.length) {
                editFieldData[0].en.values[i].value = event.target.value;
            } else {
                for ( let j = 0; j <= i; j++ ){
                    if( j >= enValue.length){
                        if( j === i ){
                            editFieldData[0].en.values.push({'value': event.target.value});
                        }else{
                            editFieldData[0].en.values.push({'value': ''});
                        }

                    }
                }

            }
            this.setState({enOptionValues: optionValues, editFieldData: editFieldData});
        } else {
            let optionValues = [...this.state.arOptionValues];
            optionValues[i] = event.target.value;
            if (i < arValue.length) {
                editFieldData[0].ar.values[i].value = event.target.value;
            } else {
                for ( let j = 0; j <= i; j++ ){
                    if( j >= arValue.length){
                        if( j === i ){
                            editFieldData[0].ar.values.push({'value': event.target.value});
                        }else{
                            editFieldData[0].ar.values.push({'value': ''});
                        }

                    }
                }
            }
            this.setState({arOptionValues: optionValues, editFieldData: editFieldData});
        }
    };
    handleChangeUploadOption = (event) => {
        let checked = event.target.checked;
        let editFieldData = this.state.editFieldData;
        const currentElement = event.currentTarget.attributes.getNamedItem('id').value;
        editFieldData[0].en.uploadOptions[currentElement] = checked;
        editFieldData[0].ar.uploadOptions[currentElement] = checked;
        this.setState({
            editFieldData: editFieldData
        });
    };

    render() {
        const {option, enOptionValues, arOptionValues} = this.state;
        return (
            <div className="modal-main">
                <div className="modal-wrap">
                    <div className="modal-inner">
                        <span className="dashicons dashicons-no-alt main-clearbtn"
                              onClick={this.handleModalClose} />
                        {0 < this.state.editFieldData.length && this.state.editFieldData.map((item, index) => {
                            const enArr = item.en;
                            const arArr = item.ar;
                            return (
                                <Fragment key={index}>
                                    <div className="add-field-wrap default-checkbox">
                                        <div className="field-label-wrap">
                                            <span>Required</span>
                                        </div>
                                        <div className="field-content-wrap">
                                            <label className="checkbox-container">
                                                <input id="input_checkbox"
                                                       type="checkbox"
                                                       checked={enArr.required}
                                                       onChange={this.handleRequiredCheck}/>
                                            </label>
                                        </div>
                                    </div>
                                    <div className="add-field-wrap label-name">
                                        <div className="field-label-wrap">
                                            <span>Label<sup className="medatory"> *</sup></span>
                                        </div>
                                        <div className="field-content-wrap">
                                            <label className="en-field" htmlFor="enLabelName">
                                                <input
                                                    type="text"
                                                    name="enLabelName"
                                                    id="enLabelName"
                                                    value={enArr.label}
                                                    className="form-control"
                                                    onChange={this.handleChange}
                                                    placeholder="Type English Label"
                                                />
                                            </label>
                                            <label className="ar-field" htmlFor="arLabelName">
                                                <input
                                                    type="text"
                                                    name="arLabelName"
                                                    id="arLabelName"
                                                    value={arArr.label}
                                                    className="form-control"
                                                    onChange={this.handleChange}
                                                    placeholder="Type Arabic Label"
                                                />
                                            </label>
                                        </div>
                                    </div>
                                    {'Text Input' === enArr.control &&
                                    <div className="validation-field">
                                        <div className="add-field-wrap">
                                            <div className="field-label-wrap">
                                                <span>Validation Type<sup className="medatory"> *</sup></span>
                                            </div>
                                            <div className="field-content-wrap">
                                                <select onChange={this.onSelectValidationType} value={enArr.type}>
                                                    <option value="">Select Validation Type</option>
                                                    <option value="text">Text</option>
                                                    <option value="mobile-number">Mobile Number</option>
                                                    <option value="number">Number</option>
                                                    <option value="date">Date</option>
                                                    <option value="time">Time</option>
                                                    <option value="email">Email Address</option>
                                                    <option value="url">URL</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    }
                                    {'Text Area' === enArr.control &&
                                    <div className="validation-field">

                                    </div>
                                    }
                                    {'Dropdown Select' === enArr.control &&
                                    <div className="validation-field">
                                        <div className="add-field-wrap">
                                            <div className="field-content-wrap">
                                                <input type="checkbox" className="dff-multiple" name="multiple"
                                                       id="multiple-allow" checked={enArr.multiple}
                                                       onChange={this.handleAllowMultiple}/>
                                                <label htmlFor="multiple-allow">Allow Multiple Selections</label>
                                            </div>
                                        </div>
                                        <div className="add-field-wrap">
                                            <div className="field-label-wrap">
                                                <span>Value <sup className="medatory"> *</sup></span>
                                            </div>
                                            <div className="field-content-wrap">
                                                <div className="multiple-option">
                                                    <div className="multiple-option-group">
                                                        {
                                                            0 < option.length && option.map((item, index) => (
                                                                <Fragment key={index}>
                                                                    <div className="option-wrap">
                                                                        <label className="en-field"
                                                                               htmlFor={`en_option_${index}`}>
                                                                            <input
                                                                                type="text"
                                                                                name={`en_option_${index}`}
                                                                                id={`en_option_${index}`}
                                                                                value={enOptionValues[index] ? enOptionValues[index] : ''}
                                                                                className="form-control"
                                                                                onChange={this.handleOptionChange.bind(this, index, 'en')}
                                                                                placeholder={item.en.label}
                                                                            />
                                                                        </label>
                                                                        <label className="ar-field"
                                                                               htmlFor={`ar_option_${index}`}>
                                                                            <input
                                                                                type="text"
                                                                                name={`ar_option_${index}`}
                                                                                id={`ar_option_${index}`}
                                                                                value={arOptionValues[index] ? arOptionValues[index] : ''}
                                                                                className="form-control"
                                                                                onChange={this.handleOptionChange.bind(this, index, 'ar')}
                                                                                placeholder={item.ar.label}
                                                                            />
                                                                        </label>
                                                                        { // TODO: "index" attr is not allowed here
                                                                         }
                                                                        <span
                                                                            className="dashicons dashicons-no-alt remove-option-text"
                                                                            index={index}
                                                                            onClick={this.deleteOption}/>
                                                                    </div>
                                                                </Fragment>
                                                            ))
                                                        }
                                                    </div>
                                                    <div className="add-new-option-text"
                                                         onClick={this.addNewOptionHandle}>
                                                        <span className="dashicons dashicons-plus-alt2" />
                                                        <span>Add Option</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    }
                                    {'Radio' === enArr.control &&
                                    <div className="validation-field">
                                        <div className="add-field-wrap">
                                            <div className="field-label-wrap">
                                                <span>Value<sup className="medatory"> *</sup></span>
                                            </div>
                                            <div className="field-content-wrap">
                                                <div className="multiple-option">
                                                    <div className="multiple-option-group">
                                                        {
                                                            0 < option.length && option.map((item, index) => (
                                                                <Fragment key={index}>
                                                                    <div className="option-wrap">
                                                                        <label className="en-field"
                                                                               htmlFor={`en_option_${index}`}>
                                                                            <input
                                                                                type="text"
                                                                                name={`en_option_${index}`}
                                                                                id={`en_option_${index}`}
                                                                                value={enOptionValues[index] ? enOptionValues[index] : ''}
                                                                                className="form-control"
                                                                                onChange={this.handleOptionChange.bind(this, index, 'en')}
                                                                                placeholder={item.en.label}
                                                                            />
                                                                        </label>
                                                                        <label className="ar-field"
                                                                               htmlFor={`en_option_${index}`}>
                                                                            <input
                                                                                type="text"
                                                                                name={`ar_option_${index}`}
                                                                                id={`ar_option_${index}`}
                                                                                value={arOptionValues[index] ? arOptionValues[index] : ''}
                                                                                className="form-control"
                                                                                onChange={this.handleOptionChange.bind(this, index, 'ar')}
                                                                                placeholder={item.ar.label}
                                                                            />
                                                                        </label>
                                                                        <span
                                                                            className="dashicons dashicons-no-alt remove-option-text"
                                                                            index={index}
                                                                            onClick={this.deleteOption} />
                                                                    </div>
                                                                </Fragment>
                                                            ))
                                                        }
                                                    </div>
                                                    <div className="add-new-option-text"
                                                         onClick={this.addNewOptionHandle}>
                                                        <span className="dashicons dashicons-plus-alt2" />
                                                        <span>Add Option</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    }
                                    {'Checkbox' === enArr.control &&
                                    <div className="validation-field">
                                        <div className="add-field-wrap">
                                            <div className="field-label-wrap">
                                                <span>Value<sup className="medatory"> *</sup></span>
                                            </div>
                                            <div className="field-content-wrap">
                                                <div className="multiple-option">
                                                    <div className="multiple-option-group">
                                                        {
                                                            0 < option.length && option.map((item, index) => (
                                                                <Fragment key={index}>
                                                                    <div className="option-wrap">
                                                                        <label className="en-field"
                                                                               htmlFor={`en_option_${index}`}>
                                                                            <input
                                                                                type="text"
                                                                                name={`en_option_${index}`}
                                                                                id={`en_option_${index}`}
                                                                                value={enOptionValues[index] ? enOptionValues[index] : ''}
                                                                                className="form-control"
                                                                                onChange={this.handleOptionChange.bind(this, index, 'en')}
                                                                                placeholder={item.en.label}
                                                                            />
                                                                        </label>
                                                                        <label className="ar-field"
                                                                               htmlFor={`ar_option_${index}`}>
                                                                            <input
                                                                                type="text"
                                                                                name={`ar_option_${index}`}
                                                                                id={`ar_option_${index}`}
                                                                                value={arOptionValues[index] ? arOptionValues[index] : ''}
                                                                                className="form-control"
                                                                                onChange={this.handleOptionChange.bind(this, index, 'ar')}
                                                                                placeholder={item.ar.label}
                                                                            />
                                                                        </label>
                                                                        <span
                                                                            className="dashicons dashicons-no-alt remove-option-text"
                                                                            index={index}
                                                                            onClick={this.deleteOption} />
                                                                    </div>
                                                                </Fragment>
                                                            ))
                                                        }
                                                    </div>
                                                    <div className="add-new-option-text"
                                                         onClick={this.addNewOptionHandle}>
                                                        <span className="dashicons dashicons-plus-alt2" />
                                                        <span>Add Option</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    }
                                    {'File Upload' === enArr.control &&
                                    <div className="validation-field">
                                        {0 < Object.keys(enArr.uploadOptions).length &&
                                        <div className="add-field-wrap">
                                            <div className="field-label-wrap">
                                                <span>Upload Options<sup className="medatory"> *</sup></span></div>
                                            <div className="field-content-wrap">
                                                <div className="multiple-check-option">
                                                    {
                                                        // TODO: Copy-paste should be removed.
                                                        //  Move repeated code in separate component with parameters
                                                    }
                                                    <div className="check-option-wrap">
                                                        <label htmlFor="pdf">
                                                            <input name="uploadOption[]"
                                                                   checked={enArr.uploadOptions.pdf}
                                                                   type="checkbox" id="pdf"
                                                                   onChange={this.handleChangeUploadOption}/>
                                                        </label> .pdf
                                                    </div>
                                                    <div className="check-option-wrap">
                                                        <label htmlFor="doc">
                                                            <input name="uploadOption[]"
                                                                   checked={enArr.uploadOptions.doc}
                                                                   type="checkbox" id="doc"
                                                                   onChange={this.handleChangeUploadOption}/>
                                                        </label> .doc
                                                    </div>
                                                    <div className="check-option-wrap">
                                                        <label htmlFor="png">
                                                            <input name="uploadOption[]"
                                                                   checked={enArr.uploadOptions.png}
                                                                   type="checkbox" id="png"
                                                                   onChange={this.handleChangeUploadOption}/>
                                                        </label> .png
                                                    </div>
                                                    <div className="check-option-wrap">
                                                        <label htmlFor="xlsx">
                                                            <input name="uploadOption[]"
                                                                   checked={enArr.uploadOptions.xlsx}
                                                                   type="checkbox" id="xlsx"
                                                                   onChange={this.handleChangeUploadOption}/>
                                                        </label> .xlsx
                                                    </div>
                                                    <div className="check-option-wrap">
                                                        <label htmlFor="pptx">
                                                            <input name="uploadOption[]"
                                                                   checked={enArr.uploadOptions.pptx}
                                                                   type="checkbox" id="pptx"
                                                                   onChange={this.handleChangeUploadOption}/>
                                                        </label> .pptx
                                                    </div>
                                                    <div className="check-option-wrap">
                                                        <label htmlFor="jpg">
                                                            <input name="uploadOption[]"
                                                                   checked={enArr.uploadOptions.jpg}
                                                                   type="checkbox" id="jpg"
                                                                   onChange={this.handleChangeUploadOption}/>
                                                        </label> .jpg
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        }
                                        <div className="add-field-wrap">
                                            <div className="field-label-wrap">
                                                <span>Button Label 1<sup className="medatory"> *</sup></span>
                                            </div>
                                            <div className="field-content-wrap">
                                                <div className="multiple-option">
                                                    <div className="multiple-option-group">
                                                        <div className="option-wrap">
                                                            <label className="en-field" htmlFor="enButtonLabel1">
                                                                <input
                                                                    type="text"
                                                                    name="enButtonLabel1"
                                                                    id="enButtonLabel1"
                                                                    value={enArr.button1}
                                                                    className="form-control"
                                                                    onChange={this.handleChange}
                                                                    placeholder="Type English Label"
                                                                />
                                                            </label>
                                                            <label className="ar-field" htmlFor="arButtonLabel1">
                                                                <input
                                                                    type="text"
                                                                    name="arButtonLabel1"
                                                                    id="arButtonLabel1"
                                                                    value={arArr.button1}
                                                                    className="form-control"
                                                                    onChange={this.handleChange}
                                                                    placeholder="Type Arabic Label"
                                                                />
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    }
                                </Fragment>
                            );
                        })
                        }
                        <div className="button-wrapper add-field-wrap">
                            <div className="field-content-wrap">
                                <input type="button" value="Save" className="button button-primary"
                                       onClick={this.handleSaveNewField}/>
                                <input type="button" value="Cancel" className="button button-primary"
                                       onClick={this.handleModalClose}/>
                            </div>
                            {this.state.errorMsg &&
                            <div className="error-msg">Please complete all required fields (indicated with *).</div>}
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default EditFieldModal;
EditFieldModal.propTypes = {
    handleEditField: PropTypes.func.isRequired,
    editFieldData: PropTypes.func.isRequired,
    handleEditModelClose: PropTypes.func.isRequired,
};
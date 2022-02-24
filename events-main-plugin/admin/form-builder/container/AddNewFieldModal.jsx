import React, {Component, Fragment} from 'react';
import PropTypes from 'prop-types';

class AddNewFieldModal extends Component {
    constructor(props) {
        super(props);
        this.state = {
            modal: false,
            enLabelName: '',
            arLabelName: '',
            enButtonLabel1: '',
            arButtonLabel1: '',
            fieldType: '',
            validationType: '',
            textFiledValue: '',
            require: false,
            multipleOption: false,
            enOptionValues: [],
            arOptionValues: [],
            errorMsg: false,
            uploadOptions: [{'pdf': true, 'doc': true, 'png': true, 'xlsx': true, 'pptx': true, 'jpg': true}],
            option: [
                {'en': {'label': 'Option1 (English)'}, 'ar': {'label': 'Option1 (Arabic)'}},
                {'en': {'label': 'Option2 (English)'}, 'ar': {'label': 'Option2 (Arabic)'}}
            ]
        };
    }

    handleChange = (event) => {
        this.setState({
            [event.target.name]: event.target.value
        });
    };
    onSelectField = (event) => {
        event.preventDefault();
        this.setState({fieldType: event.target.value});
    };
    onSelectValidationType = (event) => {
        event.preventDefault();
        this.setState({validationType: event.target.value});
    };
    handleModalClose = (event) => {
        this.props.handleModelClose();
    };
    camelCase = (str) => {
        return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function (word, index) {
            return 0 == index ? word.toLowerCase() : word.toUpperCase();
        }).replace(/\s+/g, '');
    };

    handleSaveNewField = (event) => {
        const {enLabelName, arLabelName, fieldType, require, multipleOption, validationType, option, enOptionValues, arOptionValues, enButtonLabel1, arButtonLabel1} = this.state;
        let enID, arID = '';
        this.setState({errorMsg: false});
        if ('' !== enLabelName && '' !== arLabelName && '' !== fieldType) {
            enID = this.camelCase(`en${enLabelName}`);
            arID = this.camelCase(`ar${enLabelName}`);
            if ('Text Input' === fieldType) {
                if ('' !== validationType) {
                    let newFieldObj = {
                        'en': {
                            'control': fieldType,
                            'type': validationType,
                            'required': require,
                            'label': enLabelName,
                            'id': enID,
                            'className': 'form-control',
                            'name': enID,
                            'language': 'English',
                            'abbrivation': 'en'
                        },
                        'ar': {
                            'control': fieldType,
                            'type': validationType,
                            'required': require,
                            'label': arLabelName,
                            'id': arID,
                            'className': 'form-control',
                            'name': arID,
                            'language': 'Arabic',
                            'abbrivation': 'ar'
                        }
                    };
                    this.props.handleAddField(newFieldObj);
                } else {
                    this.setState({errorMsg: true});
                }
            } else if ('Text Area' === fieldType) {
                let newFieldObj = {
                    'en': {
                        'control': fieldType,
                        'required': require,
                        'label': enLabelName,
                        'id': enID,
                        'className': 'form-control',
                        'name': enID,
                        'language': 'English',
                        'abbrivation': 'en'
                    },
                    'ar': {
                        'control': fieldType,
                        'required': require,
                        'label': arLabelName,
                        'id': arID,
                        'className': 'form-control',
                        'name': arID,
                        'language': 'Arabic',
                        'abbrivation': 'ar'
                    }
                };
                this.props.handleAddField(newFieldObj);
            } else if ('Dropdown Select' === fieldType) {
                if (0 < enOptionValues.length && 0 < arOptionValues.length ) {
                    let enValue = [];
                    let arValue = [];
                    let optionValueErrorCount = 0;
                    0 < option.length && option.map((item, index) => {
                        if( '' === enOptionValues[index] || index >= enOptionValues.length || undefined === enOptionValues[index]){
                            optionValueErrorCount++;
                        }else{
                            enValue.push({'value': enOptionValues[index]});
                        }
                        if( '' === arOptionValues[index] || index  >= arOptionValues.length  || undefined === arOptionValues[index] ){
                            optionValueErrorCount++;
                        }else{
                            arValue.push({'value': arOptionValues[index]});
                        }


                    });
                    let newFieldObj = {
                        'en': {
                            'control': fieldType,
                            'required': require,
                            'label': enLabelName,
                            'id': enID,
                            'className': 'form-control',
                            'name': enID,
                            'language': 'English',
                            'abbrivation': 'en',
                            'multiple': multipleOption,
                            'values': enValue
                        },
                        'ar': {
                            'control': fieldType,
                            'required': require,
                            'label': arLabelName,
                            'id': arID,
                            'className': 'form-control',
                            'name': arID,
                            'language': 'Arabic',
                            'abbrivation': 'ar',
                            'multiple': multipleOption,
                            'values': arValue
                        }
                    };
                    if( 0 === optionValueErrorCount ){
                        this.props.handleAddField(newFieldObj);
                    }else{
                        this.setState({errorMsg: true});
                    }
                } else {
                    this.setState({errorMsg: true});
                }

            } else if ('Radio' === fieldType) {
                if (0 < enOptionValues.length && 0 < arOptionValues.length) {
                    let enValue = [];
                    let arValue = [];
                   let optionValueErrorCount = 0;
                    0 < option.length && option.map((item, index) => {
                        if( '' === enOptionValues[index] || index >= enOptionValues.length || undefined === enOptionValues[index]){
                            optionValueErrorCount++;
                        }
                        if( '' === arOptionValues[index] || index  >= arOptionValues.length || undefined === arOptionValues[index]){
                            optionValueErrorCount++;
                        }
                        enValue.push({'value': enOptionValues[index]});
                        arValue.push({'value': arOptionValues[index]});
                    });
                    let newFieldObj = {
                        'en': {
                            'control': fieldType,
                            'required': require,
                            'label': enLabelName,
                            'id': enID,
                            'className': 'form-control',
                            'name': enID,
                            'language': 'English',
                            'abbrivation': 'en',
                            'values': enValue
                        },
                        'ar': {
                            'control': fieldType,
                            'required': require,
                            'label': arLabelName,
                            'id': arID,
                            'className': 'form-control',
                            'name': arID,
                            'language': 'Arabic',
                            'abbrivation': 'ar',
                            'values': arValue
                        }
                    };
                    if( 0 === optionValueErrorCount ){
                        this.props.handleAddField(newFieldObj);
                    }else{
                        this.setState({errorMsg: true});
                    }
                } else {
                    this.setState({errorMsg: true});
                }
            } else if ('Checkbox' === fieldType ) {
                if (0 < enOptionValues.length && 0 < arOptionValues.length) {
                    let enValue = [];
                    let arValue = [];
                    let optionValueErrorCount = 0;
                    0 < option.length && option.map((item, index) => {
                        if( '' === enOptionValues[index] || index >= enOptionValues.length || undefined === enOptionValues[index]){
                            optionValueErrorCount++;
                        }
                        if( '' === arOptionValues[index] || index  >= arOptionValues.length || undefined === arOptionValues[index]){
                            optionValueErrorCount++;
                        }
                        enValue.push({'value': enOptionValues[index]});
                        arValue.push({'value': arOptionValues[index]});
                    });
                    let newFieldObj = {
                        'en': {
                            'control': fieldType,
                            'required': require,
                            'label': enLabelName,
                            'id': enID,
                            'className': 'form-control',
                            'name': enID,
                            'language': 'English',
                            'abbrivation': 'en',
                            'values': enValue
                        },
                        'ar': {
                            'control': fieldType,
                            'required': require,
                            'label': arLabelName,
                            'id': arID,
                            'className': 'form-control',
                            'name': arID,
                            'language': 'Arabic',
                            'abbrivation': 'ar',
                            'values': arValue
                        }
                    };
                    if( 0 === optionValueErrorCount ){
                        this.props.handleAddField(newFieldObj);
                    }else{
                        this.setState({errorMsg: true});
                    }
                } else {
                    this.setState({errorMsg: true});
                }
            } else if ('File Upload' === fieldType) {
                const uploadOptions = this.state.uploadOptions[0];
                let allowedOption = [];
                for (let key in uploadOptions) {
                    if (uploadOptions.hasOwnProperty(key)) {
                        uploadOptions[key] && allowedOption.push(key);
                    }
                }
                if ('' !== enButtonLabel1 && '' !== arButtonLabel1 && 0 < allowedOption.length) {
                    let newFieldObj = {
                        'en': {
                            'control': fieldType,
                            'required': require,
                            'label': enLabelName,
                            'id': enID,
                            'className': 'form-control',
                            'name': enID,
                            'language': 'English',
                            'abbrivation': 'en',
                            'button1': enButtonLabel1,
                            'uploadOptions': uploadOptions
                        },
                        'ar': {
                            'control': fieldType,
                            'required': require,
                            'label': arLabelName,
                            'id': arID,
                            'className': 'form-control',
                            'name': arID,
                            'language': 'Arabic',
                            'abbrivation': 'ar',
                            'button1': arButtonLabel1,
                            'uploadOptions': uploadOptions
                        }
                    };
                    this.props.handleAddField(newFieldObj);
                } else {
                    this.setState({errorMsg: true});
                }
            }
        } else {
            this.setState({errorMsg: true});
        }
    };

    handleRequiredCheck = (event) => {
        let checked = event.target.checked;
        this.setState({require: checked});
    };
    handleAllowMultiple = (event) => {
        let checked = event.target.checked;
        this.setState({multipleOption: checked});
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
    deleteOption = (event) => {
        const currentIndex = event.currentTarget.attributes.getNamedItem('index').value;
        this.setState({option: this.state.option.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex))});
        this.setState({enOptionValues: this.state.enOptionValues.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex))});
        this.setState({arOptionValues: this.state.arOptionValues.filter((cItem, index) => parseInt(index) !== parseInt(currentIndex))});
    };

    handleChangeUploadOption = (event) => {
        let checked = event.target.checked;
        const currentElement = event.currentTarget.attributes.getNamedItem('id').value;
        let uploadOptions = this.state.uploadOptions;
        uploadOptions[0][currentElement] = checked;
        this.setState({uploadOptions});
    };
    handleOptionChange = (i, langaugeType, event) => {
        if ('en' === langaugeType) {
            let optionValues = [...this.state.enOptionValues];
            optionValues[i] = event.target.value;
            this.setState({enOptionValues: optionValues});
        } else {
            let optionValues = [...this.state.arOptionValues];
            optionValues[i] = event.target.value;
            this.setState({arOptionValues: optionValues});
        }
    };

    render() {
        const {enLabelName, arLabelName, fieldType, option, enOptionValues, arOptionValues, enButtonLabel1, arButtonLabel1, uploadOptions} = this.state;
        return (
            <div className="modal-main">
                <div className="modal-wrap">
                    <div className="modal-inner">
                        <span className="dashicons dashicons-no-alt main-clearbtn"
                              onClick={this.handleModalClose}></span>
                        <div className="add-field-wrap default-checkbox">
                            <div className="field-label-wrap">
                                <span>Required</span>
                            </div>
                            <div className="field-content-wrap">
                                <label htmlFor="input_checkbox" className="checkbox-container">
                                    <input id="input_checkbox" type="checkbox" onChange={this.handleRequiredCheck}/>
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
                                        value={enLabelName}
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
                                        value={arLabelName}
                                        className="form-control"
                                        onChange={this.handleChange}
                                        placeholder="Type Arabic Label"
                                    />
                                </label>
                            </div>
                        </div>
                        <div className="add-field-wrap feild-type">
                            <div className="field-label-wrap">
                                <span>Field Type<sup className="medatory"> *</sup></span>
                            </div>
                            <div className="field-content-wrap">
                                <select onChange={this.onSelectField}>
                                    <option value="">Select Field Type</option>
                                    <option value="Text Input">Text Input</option>
                                    <option value="Text Area">Text Area</option>
                                    <option value="Radio">Radio</option>
                                    <option value="Checkbox">Checkbox</option>
                                    <option value="Dropdown Select">Dropdown Select</option>
                                    <option value="File Upload">File Upload</option>
                                </select>
                            </div>
                        </div>
                        {'Text Input' === fieldType &&
                        <div className="validation-field">
                            <div className="add-field-wrap">
                                <div className="field-label-wrap">
                                    <span>Validation Type<sup className="medatory"> *</sup></span>
                                </div>
                                <div className="field-content-wrap">
                                    <select onChange={this.onSelectValidationType}>
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
                        {'Text Area' === fieldType &&
                        <div className="validation-field">

                        </div>
                        }
                        {'Dropdown Select' === fieldType &&
                        <div className="validation-field">
                            <div className="add-field-wrap">
                                <div className="field-content-wrap">
                                    <input type="checkbox" className="dff-multiple" name="multiple" id="multiple-allow"
                                           onChange={this.handleAllowMultiple}/>
                                    <label htmlFor="multiple-allow">Allow Multiple Selections</label>
                                </div>
                            </div>
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
                                                            <label className="en-field" htmlFor={`en_option_${index}`}>
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
                                                            <label className="ar-field" htmlFor={`ar_option_${index}`}>
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
                                                                index={index} onClick={this.deleteOption}></span>
                                                        </div>
                                                    </Fragment>
                                                ))
                                            }
                                        </div>
                                        <div className="add-new-option-text" onClick={this.addNewOptionHandle}>
                                            <span className="dashicons dashicons-plus-alt2"></span>
                                            <span>Add Option</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        }
                        {'Radio' === fieldType &&
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
                                                            <label className="en-field" htmlFor={`en_option_${index}`}>
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
                                                            <label className="ar-field" htmlFor={`ar_option_${index}`}>
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
                                                                index={index} onClick={this.deleteOption}></span>
                                                        </div>
                                                    </Fragment>
                                                ))
                                            }
                                        </div>
                                        <div className="add-new-option-text" onClick={this.addNewOptionHandle}>
                                            <span className="dashicons dashicons-plus-alt2"></span>
                                            <span>Add Option</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        }
                        {'Checkbox' === fieldType &&
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
                                                            <label className="en-field" htmlFor={`en_option_${index}`}>
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
                                                            <label className="ar-field" htmlFor={`ar_option_${index}`}>
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
                                                                index={index} onClick={this.deleteOption}></span>
                                                        </div>
                                                    </Fragment>
                                                ))
                                            }
                                        </div>
                                        <div className="add-new-option-text" onClick={this.addNewOptionHandle}>
                                            <span className="dashicons dashicons-plus-alt2"></span>
                                            <span>Add Option</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        }
                        {'File Upload' === fieldType &&
                        <div className="validation-field">
                            <div className="add-field-wrap">
                                <div className="field-label-wrap"><span>Upload Options<sup className="medatory"> *</sup></span>
                                </div>
                                <div className="field-content-wrap">
                                    <div className="multiple-check-option">
                                        <div className="check-option-wrap">
                                            <label htmlFor="pdf">
                                                <input name="uploadOption[]" checked={uploadOptions[0].pdf}
                                                       type="checkbox" id="pdf"
                                                       onChange={this.handleChangeUploadOption}/>
                                            </label> .pdf
                                        </div>
                                        <div className="check-option-wrap">
                                            <label htmlFor="doc">
                                                <input name="uploadOption[]" checked={uploadOptions[0].doc}
                                                       type="checkbox" id="doc"
                                                       onChange={this.handleChangeUploadOption}/>
                                            </label> .doc
                                        </div>
                                        <div className="check-option-wrap">
                                            <label htmlFor="png">
                                                <input name="uploadOption[]" checked={uploadOptions[0].png}
                                                       type="checkbox" id="png"
                                                       onChange={this.handleChangeUploadOption}/>
                                            </label> .png
                                        </div>
                                        <div className="check-option-wrap">
                                            <label htmlFor="xlsx">
                                                <input name="uploadOption[]" checked={uploadOptions[0].xlsx}
                                                       type="checkbox" id="xlsx"
                                                       onChange={this.handleChangeUploadOption}/>
                                            </label> .xlsx
                                        </div>
                                        <div className="check-option-wrap">
                                            <label htmlFor="pptx">
                                                <input name="uploadOption[]" checked={uploadOptions[0].pptx}
                                                       type="checkbox" id="pptx"
                                                       onChange={this.handleChangeUploadOption}/>
                                            </label> .pptx
                                        </div>
                                        <div className="check-option-wrap">
                                            <label htmlFor="jpg">
                                                <input name="uploadOption[]" checked={uploadOptions[0].jpg}
                                                       type="checkbox" id="jpg"
                                                       onChange={this.handleChangeUploadOption}/>
                                            </label> .jpg
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                        value={enButtonLabel1}
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
                                                        value={arButtonLabel1}
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

export default AddNewFieldModal;
AddNewFieldModal.propTypes = {
    handleAddField: PropTypes.func.isRequired,
    handleModelClose: PropTypes.func.isRequired,
};
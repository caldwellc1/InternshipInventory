import React from 'react';
import ReactDOM from 'react-dom';
import Dropzone from 'react-dropzone';
import $ from 'jquery';

/*var AffiliationList = React.createClass({
  render: function() {
    return (
     	<option value={this.props.id}>{this.props.name}</option>
    )
  }
});

var AffiliationSelected = React.createClass({
    getInitialState: function() {
        return {showContract: false,
            showAffil: false,
            affilData: null};
    },
    onAffilationSelected(){
        this.setState({showAffil: true});
    },
    componentWillMount: function(){
        this.getData();
    },
    handleDrop: function(e) {
		this.setState({dropData: e.target.value});
	},
    onSave: function(aff){
        this.setType('affiliation', aff.id);
    },
    getData: function(){
        // Grabs the affiliation data
        $.ajax({
            url: 'index.php?module=intern&action=AffiliateListRest&internshipId='+this.props.internshipId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                this.setState({affilData: data});
            }.bind(this),
            error: function(xhr, status, err) {
                alert("Failed to load affiliation data.")
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },
    render: function() {
        var aData = null;
        if (this.state.affilData != null) {
			aData = this.state.deptData.map(function (affilData) {
			return (
					<AffiliationList key={affilData.id}
						name={affilData.name}
						begin={affilData.begin_date}
                        end={affilData.end_date}/>
				);
			});
		} else {
			aData = "";
		}
        return (
            <div>
                <select className="form-control" onChange={this.handleDrop}>
                    {aData}
                </select>
            </div>
        );
    }
});

var ContractSelected = React.createClass({
    getInitialState: function() {
        return {showContract: false,
            showAffil: false};
    },
    onContractSelect(){
        this.setState({showContract: true});
    },
    getDefaultProps: function(){
        return{doc: []}
    },
    onDrop: function(doc){
        this.props.update(doc);
    },
    onOpenClick: function(){
        this.refs.dropzone.open();
    },
    onSave: function(){
        this.setType('contract');
    },
    render: function() {
        var doc;
        if(this.props.doc.length > 0){

        } else {
            doc = (
                <div className="clickme">
                    <i class="fa fa-file"></i><br/>
                    <p>Click or drag document here.</p>
                </div>
            );
        }
        return (
            <div>
                <div className="row">
                    <Dropzone ref="dropzone" onDrop={this.onDrop} className="dropzone text-center">
                        {doc}
                    </Dropzone>
                </div>
            </div>
        );
    }
});*/

var ContractAffiliation = React.createClass({
    /*getInitialState: function() {
        return {showContract: false,
            showAffil: false};
    },
    setType: function(type, id){
        //send ajax to set type & id
        if(id == null){
            //contract type set
        }else{
            //affiliation type set
        }
    },*/
    render: function() {
        return (
            <div className="row">
                <div className="col-lg-6">
                    <div class="btn-group" data-toggle="buttons" role="group">
                        <label class="btn btn-primary active">
                            <input type="radio" name="option" autocomplete="off" checked onChange={this.onContractSelect}> Contract</input>
                        </label>
                        <label class="btn btn-primary">
                            <input type="radio" name="option" autocomplete="off" onChange={this.onAffilationSelected}> Affiliation Agreement</input>
                        </label>
                    </div>
                </div>
            </div>
        );
    }
});
//<ContractSelected show={this.state.showContract} />
//<AffiliationSelected show={this.state.showAffil} />

var Test = React.createClass({
    render: function() {
        return (
            <div>
                <p>Test Contract</p>
            </div>
        );
    }
});

ReactDOM.render(
    <ContractAffiliation internshipId={window.internshipId}/>,
    document.getElementById('contract-affiliation')
);
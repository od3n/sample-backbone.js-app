<!DOCTYPE HTML>
<html>
    <head>
        <title>Sample Backbone App</title>
        <link href="/assets/css/bootstrap-combined.min.css" rel="stylesheet">
        <script src="/assets/js/jquery.min.js"></script>
        <script src="/assets/js/underscore-min.js"></script>
        <script src="/assets/js/backbone-min.js"></script>

    </head>
    <body>

        <!-- All underscore templates for simple app-->

        <!-- Underscore template for index page -->

        <script type="text/template" id="index_template">
            <a href="#new" class="btn btn-info">Add New</a>
            <hr/>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Job</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <% _.each(persons, function(person){  %>
                    <tr>
                        <td><%= person.get('name') %></td>
                        <td><%= person.get('age') %></td>
                        <td><%= person.get('job') %></td>
                        <td><a href='#edit/<%= person.get('id') %>' class="btn btn-inverse">Edit</a></td>
                    </tr>
                    <% }); %>
                </tbody>
            </table>
        </script>

        <!-- Underscore template to add new person -->

        <script type="text/template" id="add_person_template">
            <form class="form">
                <legend>Add New Person</legend>
                <label>Name</label>
                <input type="text" id="name" />
                <label>Age</label>
                <input type="text" id="age" />
                <label>Job</label>
                <input type="text" id="job" />
                <hr/>
                <input type="button" value="Add" class="btn add" />
                <input type="button" value="Cancel" class="btn cancel" />
            </form>
        </script>

        <!-- Underscore template to edit person -->

        <script type="text/template" id="edit_person_template">
            <form class="form">
                <legend>Edit Person Data</legend>
                <label>Name</label>
                <input type="text" id="name" value="<%= p.get('name') %>" />
                <label>Age</label>
                <input type="text" id="age" value="<%= p.get('age') %>" />
                <label>Job</label>
                <input type="text" id="job" value="<%= p.get('job') %>" />
                <input type="hidden" id="id" value="<%= p.get('id') %>" />
                <hr/>
                <input type="button" value="Update" class="btn update" />
                <input type="button" value="Delete" class="btn btn-danger delete" />
            </form>
        </script>

        <!-- Main conainer where underscore templates will be loaded -->

        <div class="container">
            <h1>Person Data System</h1>
            <hr/>
            <div class="page"></div>
        </div>

        <!-- Javascrit code. Writing all backbone model, collections and views -->

        <script type="text/javascript">
                
            //Backbone Person Modal
                
            Person = Backbone.Model.extend({
                urlRoot: 'api.php/person'
                    
            });
                
            //Backbone Person Collection
                
            Persons = Backbone.Collection.extend({
                url: 'api.php/person'
            });
                
                
            //Backbone Index view, which will load underscore index template
                
            indexView = Backbone.View.extend({
                el: $('.page'),
                initialize: function(){
                    this.render();
                },
                render: function(){
                    var that = this;
                    var persons = new Persons();
                    persons.fetch({
                        success: function(){
                            var temp = _.template($('#index_template').html(), {persons: persons.models});
                            that.$el.html(temp);
                        }
                    });
                        
                }
            });
                
            //Backbone Add Person view, which will load underscore Add Person template
                
            addPersonView = Backbone.View.extend({
                el: $('.page'),
                initialize: function(){
                    this.render();
                },
                render: function(){
                    var temp = _.template($('#add_person_template').html(), {});
                    this.$el.html(temp);
                },
                events:{
                    'click .add' : 'addPerson',
                    'click .cancel' : 'cancelAddPerson'
                },
                addPerson: function(){
                    var name1 = $('#name').val();
                    var age1 = $('#age').val();
                    var job1 = $('#job').val();
                        
                    if(name1.length < 1 || age1.length < 1 || job1.length < 1){
                        alert('Please fill in form first!');
                    }else{
                        var person = new Person({name: name1, age: age1, job: job1});
                        person.save(null,{
                            success: function(){
                                router.navigate('', {trigger:true});
                            }
                        });
                            
                    }
                },
                cancelAddPerson: function(){
                    router.navigate('',{trigger:true});
                }
            });
                
                
            //Backbone Edit Person view, which will load underscore Edit Person template
                
            editPersonView = Backbone.View.extend({
                el: $('.page'),
                initialize: function(){
                    //this.render();
                },
                render: function(options){
                    var that = this;
                    var person = new Person({id: options.id});
                    person.fetch({
                        success: function(){
                            var temp = _.template($('#edit_person_template').html(), {p: person});
                            that.$el.html(temp);
                        }    
                    });
                        
                },
                events:{
                    'click .update' : 'updatePerson',
                    'click .delete' : 'deletePerson'
                },
                updatePerson: function(){
                    var id1 = $('#id').val();
                    var name1 = $('#name').val();
                    var age1 = $('#age').val();
                    var job1 = $('#job').val();
                        
                    var person = new Person({id: id1, name: name1, age: age1, job: job1});
                    person.save(null,{
                        success: function(){
                            router.navigate('', {trigger:true});
                        }    
                    });
                        
                },
                deletePerson: function(){
                    var id1 = $('#id').val();
                        
                    var person = new Person({id: id1});
                    person.destroy({
                        success: function(){
                            router.navigate('', {trigger:true});
                        }    
                    });
                        
                }
            });
                
                
            //Backbone Router to handle view navigation in our app
                
            MyRouter = Backbone.Router.extend({
                routes:{
                    '' : 'index',
                    'new' : 'addPerson',
                    'edit/:id' : 'editPerson'
                }
            });
                
            var ind = new indexView();
            var addP = new addPersonView();
            var editP = new editPersonView();
                
            var router = new MyRouter();
                
            router.on('route:index', function(){
                ind.render();      
            });
                
            router.on('route:addPerson', function(){
                addP.render();
            });
                
            router.on('route:editPerson', function(id){
                editP.render({id:id});
            });
                
            Backbone.history.start();
        </script>

    </body>
</html>
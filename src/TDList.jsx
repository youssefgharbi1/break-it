import ListInput from "./ListInput.jsx"
import TDElement from "./TDElement.jsx"
import { useState } from "react"
function TDList(){
    
    let [tasks, setTasks] = useState([{ task: "task", date: new Date().toLocaleDateString() }]);
    let [task, setTask] = useState("");

    function addTask(){ 
        const newtask = {task : task, date: new Date().toLocaleDateString()}
        setTasks((t) => (t = [...tasks, newtask]));
        setTask("")
    }
    function deleteTask(index){ 
        setTasks((t) =>t.filter((_, i)=> i !== index ))
    }
    function moveTaskUp(index){
        setTasks(() => [tasks[index], ...tasks.filter((_, i)=> i !== index)])
    }
    function MoveTaskDown(index){
        setTasks(() => [...tasks.filter((_, i)=> i !== index), tasks[index]])
    }
    return (
        <div>
            <ListInput addtask = {addTask}
             settask = {setTask}
             taskInput={task} />
            <ul>
                {tasks.map((element, index) => (
                    <TDElement key={index}
                    index={index}
                    task={element.task}
                    date={new Date().toLocaleTimeString()}
                    deletetask={deleteTask}
                    moveTaskUp={moveTaskUp}
                    MoveTaskDown={MoveTaskDown}
                     />
                ))}
            
            </ul>
        </div>

    )
}
export default TDList
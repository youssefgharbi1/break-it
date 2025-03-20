
function ListInput({addtask, settask, taskInput}){
    return(
        <>
        
        <input type="text" id="task-input" placeholder="Enter task :"
         value={taskInput}
         onChange={(event) => settask(event.target.value,)} 
         onKeyDown={(e) => {if (e.key === "Enter"){addtask();} }}
         />

        <button onClick={addtask}>
            add Task</button>

        </>
    )
}
export default ListInput
import "./assets/TDElement.css"
import upArrow from "./assets/up-arrow.svg";
import downArrow from "./assets/down-arrow.svg";
import deleteb from "./assets/trash-can.svg";

function TDElement({task, date, index, deletetask, moveTaskUp, MoveTaskDown}){
    return(
        <div className="task-container">
            <li key={index}>{task}</li>
            <button onClick={() => deletetask(index)}>
                <img src={deleteb} alt="delete" />
            </button>
            <button onClick={() => moveTaskUp(index)} className="task-button">
                <img src={upArrow} alt="up Arrow" />
            </button>
            <button onClick={() => MoveTaskDown(index)} className="task-button">
                <img src={downArrow} alt="down Arrow" />
            </button>
            <label>{date}</label>
        </div>
    );
}
export default TDElement;
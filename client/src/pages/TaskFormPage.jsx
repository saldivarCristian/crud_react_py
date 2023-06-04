import { useForm } from "react-hook-form";
import { createTask, deleteTask, geTask, updateTask } from "../api/tasks.api";
import { useNavigate,useParams } from "react-router-dom";
import { useEffect } from "react";
import { toast } from "react-hot-toast";


export function TaskFormPage() {
    const {
        register,
        handleSubmit, 
        formState:{ errors },
        setValue
    
    } = useForm()
    const navigate = useNavigate()
    const params = useParams()
    const onSubmit = handleSubmit( async data => {
        if( params.id ){
            const res = await updateTask(params.id, data)
            console.log(res)
            toast.success('tarea actualizada')
        }else{
            const res = await createTask(data)
            console.log(res)
            toast.success('tarea creada')

        }
        navigate("/")
    }) 

    useEffect(()=>{
        async function loadTask(){
            if( params.id ){
               const res =  await geTask(params.id)
                setValue('title',res.data.title)
                setValue('description',res.data.description)
            }
        }
        loadTask()
    },[])

    return (
        <div>
            <form onSubmit={onSubmit}>
                <input type="text" placeholder="title" {...register("title",{required:true})} />
                {errors.title && <span> Error</ span>}
                <textarea name="" id="" placeholder="Description" cols="30" rows="10"  {...register("description",{required:true})}></textarea>
                {errors.description && <span> Error</ span>}
                <button>Save</button>
            </form>
            { params.id &&  <button onClick={ async()=>{
                const accepted  = window.confirm("are you sure?")
                if( accepted ){
                     await deleteTask( params.id)
                     navigate("/tasks")
                }
            }}>Delete</button> }
            
        </div>
    )
}
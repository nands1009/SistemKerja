<style>
    /* General card styling */
/* General styles for card container */
.card-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 7px;
    gap: 30px;
    flex-wrap: wrap;
    position: relative;
    top: -331px;
    right: -64px;/* To ensure it’s responsive and stacks on smaller screens */
}

/* Styling for each card item */
.card-item {
    background-color: #ffffff;
    padding: -16px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 206px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 102px;
}

.card-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* Styling for the 'SURAT PERINGATAN' section */
.suratperingatan {
    font-size: 22px;
    color: #333;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.suratperingatan i {
    position: relative;
    font-size: 30px;
    color:  #FF2E00;
    margin-bottom: 10px;
    right: -80px;
    top: -3px;


}

.penghargaanHRD i{

    position: relative;
    font-size: 30px;
    color:#00BFA6;
    right: -80px;
    top: -3px;
}

.spterima {
    font-size: 16px;
    color: #e74c3c;
    font-weight: bold;
    margin-top: 10px;
}

/* Styling for the 'PENGHARGAAN' section */
.penghargaanHRD {
    font-size: 22px;
    color: #333;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.penghargaanHRD p {
    font-size: 24px;
    font-weight: bold;
/* Text color for 'PENGHARGAAN' */
}

.penghargaan-text {
    font-size: 16px;
    font-weight: bold;
    margin-top: 10px;
}
#suratperingatan{
    position: relative;
    font-size: 37px;
    top: -24px;
}
#penghargaanHRD{
position: relative;
    font-size: 37px;
top: -13px;
}

span[class="spterimasp"]{
position: relative;
    top: -16px;
    font-size: 13px;
    font-family: 'Arial Narrow', sans-serif;
}

span[class="penghargaan-text"]{
    position: relative;
top: -17px;
    font-size: 13px;
    font-family: 'Arial Narrow', sans-serif;
}
</style>
<tbody>
    <div class="card-container">
        <div class="card-item">
            <h1 class="suratperingatan">
                <i class="fa-solid fa-file-circle-exclamation"></i>
                <p id="suratperingatan"><?= $spapproved; ?></p>
                <span class="spterimasp">Approved PERINGATAN</span>
            </h1>
        </div>
        <div class="card-item">
            <h1 class="penghargaanHRD">
                <i class="fa-solid fa-file-circle-check"></i>
                <p id="penghargaanHRD"><?= $penghargaanapproved; ?></p>
                <span class="penghargaan-text">Approved PENGHARGAAN</span>
            </h1>
        </div>
    </div>
</tbody>
